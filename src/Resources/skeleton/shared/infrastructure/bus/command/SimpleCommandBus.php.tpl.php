<?php echo "<?php\n"; ?>

namespace App\Shared\Infrastructure\Command\Bus;

use App\Shared\Domain\Bus\Command\Command;
use App\Shared\Domain\Bus\Command\CommandBus;
use ReflectionClass;
use RuntimeException;

final class SimpleCommandBus implements CommandBus
{
    /** @var array<class-string, callable> */
    private array $map = [];

    /** @param iterable<object> $handlers */
    public function __construct(iterable $handlers)
    {
        foreach ($handlers as $handler) {
            $ref = new ReflectionClass($handler);
            $invoke = $ref->getMethod('__invoke');
            $params = $invoke->getParameters();
            if (!$params || !$params[0]->getType()) {
                continue;
            }
            /** @var class-string $msg */
            $msg = $params[0]->getType()->getName();
            $this->map[$msg] = [$handler, '__invoke'];
        }
    }

    public function dispatch(Command $command): void
    {
        $cls = $command::class;
        if (!isset($this->map[$cls])) {
            throw new RuntimeException("No command handler for $cls");
        }
        ($this->map[$cls])($command);
    }
}
