services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\Contexts\*\Application\Command\*\*Handler:
        resource: '%kernel.project_dir%/src/Contexts/*/Application/Command/*/*Handler.php'
        tags: ['app.command_handler']

    App\Contexts\*\Application\Query\*\*Handler:
        resource: '%kernel.project_dir%/src/Contexts/*/Application/Query/*/*Handler.php'
        tags: ['app.query_handler']

    App\Contexts\*\Application\Event\*\*Handler:
        resource: '%kernel.project_dir%/src/Contexts/*/Application/Event/*/*Handler.php'
        tags: ['app.event_handler']

    App\Shared\Infrastructure\Bus\SimpleCommandBus:
        arguments: { $handlers: !tagged_iterator 'app.command_handler' }

    App\Shared\Infrastructure\Bus\SimpleQueryBus:
        arguments: { $handlers: !tagged_iterator 'app.query_handler' }

    App\Shared\Infrastructure\Bus\SimpleEventBus:
        arguments: { $handlers: !tagged_iterator 'app.event_handler' }

    App\Shared\Domain\Bus\Command\CommandBus: '@App\Shared\Infrastructure\Bus\SimpleCommandBus'
    App\Shared\Domain\Bus\Query\QueryBus: '@App\Shared\Infrastructure\Bus\SimpleQueryBus'
    App\Shared\Domain\Bus\Event\EventBus: '@App\Shared\Infrastructure\Bus\SimpleEventBus'

    App\Shared\Domain\Clock\Clock: '@App\Shared\Infrastructure\Time\SystemClock'

    App\Shared\Application\Transaction\Transactionally: ~
