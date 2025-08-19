<?php echo "<?php\n"; ?>

namespace App\Shared\Infrastructure\Query\Bus;

use App\Shared\Domain\Bus\Query\Query;
use App\Shared\Domain\Bus\Query\QueryBus;
use ReflectionClass;
use RuntimeException;

final class SimpleQueryBus implements QueryBus
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

    public function ask(Query $query): mixed
    {
        $cls = $query::class;
        if (!isset($this->map[$cls])) {
            throw new RuntimeException("No query handler for $cls");
        }
        return ($this->map[$cls])($query);
    }
}
