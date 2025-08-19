<?php

declare(strict_types=1);

namespace Keyxmare\DomainDrivenDesignMakerBundle\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;

final class MakeBoundedContext extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:domain-driven-design:bounded-context';
    }

    public function __construct(
        private FileManager $fileManager,
        private ParameterBagInterface $params,
    ) {}

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Crée le squelette d’un Bounded Context (DDD/Clean)')
            ->addArgument('name', InputArgument::REQUIRED, 'Nom du bounded context (ex: Billing)')
            ->addOption('config', null, InputOption::VALUE_REQUIRED, 'Chemin d’un fichier YAML/JSON')
            ->addOption('base-path', null, InputOption::VALUE_REQUIRED, 'Base path du BC')
            ->addOption('base-namespace', null, InputOption::VALUE_REQUIRED, 'Base namespace du BC')
            ->addOption('route-prefix', null, InputOption::VALUE_REQUIRED, 'Préfixe de route')
            ->addOption('template-root', null, InputOption::VALUE_REQUIRED, 'Racine des templates')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Simule sans écrire')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Écrase les fichiers')
            ->addOption('no-shared', null, InputOption::VALUE_NONE, 'N’écrit pas la couche Shared')
            ->addOption('no-routes', null, InputOption::VALUE_NONE, 'N’écrit pas config routes')
            ->addOption('no-services', null, InputOption::VALUE_NONE, 'N’écrit pas config services')
            ->addOption('binding', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, "Binding interface=impl (répétable)");
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if (!$input->getArgument('name')) {
            $input->setArgument('name', $io->ask('Nom du bounded context', 'Billing'));
        }
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $context = (string) $input->getArgument('name');
        $slug    = strtolower($context);

        $cfg = $this->loadConfig($input, $context, $slug);
        $tplVars = $this->buildTemplateVars($cfg);

        if ($cfg['shared']['enabled'] ?? true) {
            $this->ensureOrGenerate(
                $generator,
                $cfg['shared']['files'] ?? [],
                $cfg,
                onlyIfMissing: (bool)($cfg['shared']['only_if_missing'] ?? true)
            );
        }

        foreach (['application','domain','infrastructure','interface_http'] as $section) {
            if (($cfg[$section]['enabled'] ?? true) === false) {
                continue;
            }
            $this->ensureOrGenerate($generator, $cfg[$section]['files'] ?? [], $cfg, false, $io);
        }

        $this->maybeWriteProjectFile($generator, $cfg, 'routes', (bool)($cfg['options']['write_routes'] ?? true));
        $this->maybeWriteProjectFile($generator, $cfg, 'services', (bool)($cfg['options']['write_services'] ?? true));

        $this->ensureBindings($cfg);

        if (!($cfg['options']['dry_run'] ?? false)) {
            $generator->writeChanges();
        }

        $io->success(sprintf(
            "Tout est câblé pour %s ✅  (POST /%s/samples, GET /%s/samples/{id})",
            $context,
            $cfg['vars']['route_prefix'],
            $cfg['vars']['route_prefix']
        ));
    }

    private function ensureOrGenerate(Generator $generator, array $targetToTpl, array $cfg, bool $onlyIfMissing = false, ?ConsoleStyle $io = null): void
    {
        $dryRun  = (bool)($cfg['options']['dry_run'] ?? false);
        $tplVars = $this->buildTemplateVars($cfg);

        foreach ($targetToTpl as $target => $tpl) {
            $target = $this->interpolate($target, $cfg['vars']);
            $tpl    = $this->interpolate($tpl,    $cfg['vars']);

            $force        = (bool)($cfg['options']['force'] ?? false);
            $skipIfExists = (bool)($cfg['options']['skip_if_exists'] ?? true);

            $shouldWrite = $force || !$skipIfExists || !file_exists($target);
            if ($onlyIfMissing) {
                $shouldWrite = !file_exists($target);
            }

            
            if ($shouldWrite && !$dryRun) {
                $generator->generateFile(
                    $target,
                    $this->tplFrom($cfg['vars']['template_root'], $tpl),
                    $tplVars
                );
            }
        }
    }


    private function maybeWriteProjectFile(Generator $generator, array $cfg, string $which, bool $enabled): void
    {
        if (!$enabled) {
            return;
        }
        $node = $cfg['project'][$which] ?? null;
        if (!$node) return;

        $path     = $this->interpolate($node['path'], $cfg['vars']);
        $template = $this->tplFrom($cfg['vars']['template_root'], $this->interpolate($node['template'], $cfg['vars']));
        $dryRun   = (bool)($cfg['options']['dry_run'] ?? false);

        if (!file_exists($path) && !$dryRun) {
            $generator->generateFile($path, $template, $cfg['vars']);
        }
    }

    private function ensureBindings(array $cfg): void
    {
        $services = $cfg['project']['services']['path'] ?? 'config/services/contexts.yaml';
        if (!file_exists($services)) {
            return;
        }
        $bindings = $cfg['project']['services']['bindings'] ?? [];

        foreach ((array)($cfg['cli']['bindings'] ?? []) as $pair) {
            if (str_contains($pair, '=')) {
                [$i, $impl] = explode('=', $pair, 2);
                $bindings[trim($i)] = trim($impl);
            }
        }

        if (!$bindings) {
            return;
        }

        $content = file_get_contents($services) ?: '';
        $updated = $content;

        foreach ($bindings as $iface => $impl) {
            $iface = $this->interpolate($iface, $cfg['vars']);
            $impl  = $this->interpolate($impl,  $cfg['vars']);

            $needle = sprintf("%s: '@%s'", $iface, $impl);
            if (!str_contains($content, $needle)) {
                $updated .= "\n  {$iface}: '@{$impl}'\n";
            }
        }

        if ($updated !== $content && !($cfg['options']['dry_run'] ?? false)) {
            $this->fileManager->dumpFile($services, $updated);
        }
    }

    private function loadConfig(InputInterface $input, string $context, string $slug): array
    {
        $bundleDefaults = (array) $this->params->get('keyxmare.domain-driven-design-maker.config');

        $runtime = [
            'vars' => [
                'context' => $context,
                'slug'    => $slug,
            ],
        ];

        $fromFile = [];
        $path = $input->getOption('config') ?? null;
        if ($path && is_file($path)) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (in_array($ext, ['yaml','yml'], true)) {
                $parsed = \Symfony\Component\Yaml\Yaml::parseFile($path);
                $fromFile = $parsed['domain-driven-design-maker'] ?? [];
            } elseif ($ext === 'json') {
                $parsed = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
                $fromFile = $parsed['domain-driven-design-maker'] ?? [];
            }
        }

        // 4) Overrides CLI
        $cli = [
            'vars' => array_filter([
                'base_path'      => $input->getOption('base-path'),
                'base_namespace' => $input->getOption('base-namespace'),
                'route_prefix'   => $input->getOption('route-prefix'),
                'template_root'  => $input->getOption('template-root'),
            ], static fn($v) => null !== $v),
            'options' => array_filter([
                'dry_run'        => $input->getOption('dry-run') ?: null,
                'force'          => $input->getOption('force') ?: null,
                'write_routes'   => $input->getOption('no-routes') ? false : null,
                'write_services' => $input->getOption('no-services') ? false : null,
            ], static fn($v) => null !== $v),
            'cli' => [
                'bindings' => $input->getOption('binding') ?? [],
            ],
        ];
        if ($input->getOption('no-shared')) {
            $cli['shared']['enabled'] = false;
        }

        $cfg = $this->arrayMergeRecursiveDistinct($bundleDefaults, $runtime, $fromFile, $cli);

        $cfg['vars']['base'] = $this->interpolate($cfg['vars']['base_path'], $cfg['vars']);

        $cfg['vars']['route_prefix'] = $cfg['vars']['route_prefix'] ?? '{slug}';
        $cfg['vars']['route_prefix'] = $this->interpolate($cfg['vars']['route_prefix'], $cfg['vars']);

        return $cfg;
    }

    private function arrayMergeRecursiveDistinct(array ...$arrays): array
    {
        $base = array_shift($arrays) ?? [];
        foreach ($arrays as $append) {
            foreach ($append as $k => $v) {
                if (is_array($v) && isset($base[$k]) && is_array($base[$k])) {
                    $base[$k] = $this->arrayMergeRecursiveDistinct($base[$k], $v);
                } else {
                    $base[$k] = $v;
                }
            }
        }
        return $base;
    }

    private function interpolate(string $subject, array $vars): string
    {
        return preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', static function ($m) use ($vars) {
            return $vars[$m[1]] ?? $m[0];
        }, $subject);
    }

    private function tplFrom(string $root, string $rel): string
    {
        if (str_starts_with($rel, '/')) {
            return $rel;
        }
        return rtrim($root, '/').'/'.ltrim($rel, '/');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    private function buildTemplateVars(array $cfg): array
    {
        $v = $cfg['vars'] ?? [];

        // variables historiques attendues par tes *.tpl.php
        $compat = [
            'bounded_context' => $v['context']       ?? null,
            'slug'            => $v['slug']          ?? null,
            'routePrefix'     => $v['route_prefix']  ?? null,
            'base'            => $v['base']          ?? null,
        ];

        // on expose aussi toutes les vars modernes (base_path, base_namespace, etc.)
        // en direct, sans l’enveloppe "vars"
        return array_merge($v, $compat);
    }
}
