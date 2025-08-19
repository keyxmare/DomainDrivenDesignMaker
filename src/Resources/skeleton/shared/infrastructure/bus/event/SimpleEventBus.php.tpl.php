<?php echo "<?php\n"; ?>

namespace App\Shared\Infrastructure\Event\Bus;

use App\Shared\Domain\Bus\Event\Event;
use App\Shared\Domain\Bus\Event\EventBus;
use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

final class SimpleEventBus implements EventBus
{
    /** @var array<class-string, list<callable>> */
    private array $map = [];

    /** @param iterable<object> $handlers */
    public function __construct(iterable $handlers)
    {
        foreach ($handlers as $handler) {
            $ref = new ReflectionClass($handler);
            $m = $ref->getMethod('__invoke');
            $p = $m->getParameters();
            if (!$p) { continue; }
            $t = $p[0]->getType();
            if (!$t instanceof ReflectionNamedType) { continue; }
            $eventClass = $t->getName();
            if (!is_a($eventClass, Event::class, true)) { continue; }
            $this->map[$eventClass] ??= [];
            $this->map[$eventClass][] = [$handler, '__invoke'];
        }
    }

    public function publish(Event ...$events): void
    {
        foreach ($events as $event) {
            $cls = $event::class;
            $handlers = $this->map[$cls] ?? [];
            if (!$handlers) {
                continue;
            }
            foreach ($handlers as $h) { $h($event); }
        }
    }
}
