<?php

declare(strict_types=1);

namespace Keyxmare\DomainDrivenDesignMakerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('domain-driven-design-maker');
        $root = $treeBuilder->getRootNode();

        $root
            ->addDefaultsIfNotSet()
            ->children()
            ->arrayNode('vars')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('base_namespace')->defaultValue('App\\Contexts')->end()
            ->scalarNode('base_path')->defaultValue('src/Contexts/{context}')->end()
            ->scalarNode('route_prefix')->defaultValue('{slug}')->end()
            ->scalarNode('template_root')->defaultValue('%kernel.project_dir%/vendor/keyxmare/ddd-maker-bundle/src/Resources/skeleton')->end()
            ->end()
            ->end()
            ->arrayNode('options')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('dry_run')->defaultFalse()->end()
            ->booleanNode('force')->defaultFalse()->end()
            ->booleanNode('skip_if_exists')->defaultTrue()->end()
            ->booleanNode('write_routes')->defaultTrue()->end()
            ->booleanNode('write_services')->defaultTrue()->end()
            ->end()
            ->end()
            ->arrayNode('shared')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('enabled')->defaultTrue()->end()
            ->booleanNode('only_if_missing')->defaultTrue()->end()
            ->arrayNode('files')
            ->useAttributeAsKey('target')
            ->scalarPrototype()->end()
            ->defaultValue([
                'src/Shared/Application/Transaction/Transactionally.php' => 'shared/application/transaction/Transactionally.php.tpl.php',
                'src/Shared/Domain/Bus/Command/Command.php' => 'shared/domain/bus/command/BusCommand.php.tpl.php',
                'src/Shared/Domain/Bus/Command/CommandBus.php' => 'shared/domain/bus/command/CommandBus.php.tpl.php',
                'src/Shared/Domain/Bus/Event/Event.php' => 'shared/domain/bus/event/BusEvent.php.tpl.php',
                'src/Shared/Domain/Bus/Event/EventBus.php' => 'shared/domain/bus/event/EventBus.php.tpl.php',
                'src/Shared/Domain/Bus/Query/Query.php' => 'shared/domain/bus/query/BusQuery.php.tpl.php',
                'src/Shared/Domain/Bus/Query/QueryBus.php' => 'shared/domain/bus/query/QueryBus.php.tpl.php',
                'src/Shared/Domain/Clock/Clock.php' => 'shared/domain/clock/Clock.php.tpl.php',
                'src/Shared/Infrastructure/Bus/Event/SimpleEventBus.php' => 'shared/infrastructure/bus/event/SimpleEventBus.php.tpl.php',
                'src/Shared/Infrastructure/Bus/SimpleCommandBus.php' => 'shared/infrastructure/bus/command/SimpleCommandBus.php.tpl.php',
                'src/Shared/Infrastructure/Bus/SimpleQueryBus.php' => 'shared/infrastructure/bus/query/SimpleQueryBus.php.tpl.php',
                'src/Shared/Infrastructure/Time/SystemClock.php' => 'shared/infrastructure/time/SystemClock.php.tpl.php',
            ])
            ->end()
            ->end()
            ->end()
            ->arrayNode('application')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('enabled')->defaultTrue()->end()
            ->arrayNode('files')
            ->useAttributeAsKey('target')
            ->scalarPrototype()->end()
            ->defaultValue([
                '{base}/Application/Command/CreateSample/CreateSampleCommand.php' => 'application/command/CreateSample/Command.php.tpl.php',
                '{base}/Application/Command/CreateSample/CreateSampleHandler.php' => 'application/command/CreateSample/Handler.php.tpl.php',
                '{base}/Application/DTO/SampleDTO.php' => 'application/dto/DTO.php.tpl.php',
                '{base}/Application/Event/OnSampleCreated/OnSampleCreatedHandler.php' => 'application/event/OnSampleCreated/Handler.php.tpl.php',
                '{base}/Application/Query/GetSample/GetSampleHandler.php' => 'application/query/GetSample/Handler.php.tpl.php',
                '{base}/Application/Query/GetSample/GetSampleQuery.php' => 'application/query/GetSample/Query.php.tpl.php',
            ])
            ->end()
            ->end()
            ->end()
            ->arrayNode('domain')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('enabled')->defaultTrue()->end()
            ->arrayNode('files')
            ->useAttributeAsKey('target')
            ->scalarPrototype()->end()
            ->defaultValue([
                '{base}/Domain/Event/SampleCreated.php' => 'domain/event/SampleCreated.php.tpl.php',
                '{base}/Domain/Model/Sample.php' => 'domain/model/Entity.php.tpl.php',
                '{base}/Domain/Model/SampleId.php' => 'domain/model/EntityId.php.tpl.php',
                '{base}/Domain/Repository/SampleRepository.php' => 'domain/repository/RepositoryPort.php.tpl.php',
            ])
            ->end()
            ->end()
            ->end()
            ->arrayNode('infrastructure')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('enabled')->defaultTrue()->end()
            ->arrayNode('files')
            ->useAttributeAsKey('target')
            ->scalarPrototype()->end()
            ->defaultValue([
                '{base}/Infrastructure/Persistence/InMemory/InMemorySampleRepository.php' =>
                    'infrastructure/persistence/inmemory/InMemoryRepository.php.tpl.php',
            ])
            ->end()
            ->end()
            ->end()
            ->arrayNode('interface_http')
            ->addDefaultsIfNotSet()
            ->children()
            ->booleanNode('enabled')->defaultTrue()->end()
            ->arrayNode('files')
            ->useAttributeAsKey('target')
            ->scalarPrototype()->end()
            ->defaultValue([
                '{base}/Interface/Http/Controller/CreateSamplePostController.php' =>
                    'interface/http/controller/CreateSamplePostController.php.tpl.php',
                '{base}/Interface/Http/Controller/GetSampleGetController.php' =>
                    'interface/http/controller/GetSampleGetController.php.tpl.php',
            ])
            ->end()
            ->end()
            ->end()
            ->arrayNode('project')
            ->addDefaultsIfNotSet()
            ->children()
            ->arrayNode('routes')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('path')->defaultValue('config/routes/contexts.yaml')->end()
            ->scalarNode('template')->defaultValue('project/routes/contexts.yaml.tpl.php')->end()
            ->end()
            ->end()
            ->arrayNode('services')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('path')->defaultValue('config/services/contexts.yaml')->end()
            ->scalarNode('template')->defaultValue('project/services/contexts.yaml.tpl.php')->end()
            ->arrayNode('bindings')
            ->useAttributeAsKey('interface')
            ->scalarPrototype()->end()
            ->defaultValue([
                'App\\Contexts\\{context}\\Domain\\Repository\\SampleRepository' =>
                    'App\\Contexts\\{context}\\Infrastructure\\Persistence\\InMemory\\InMemorySampleRepository',
            ])
            ->end()
            ->end()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
