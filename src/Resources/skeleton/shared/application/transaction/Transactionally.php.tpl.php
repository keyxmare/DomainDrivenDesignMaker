<?php echo "<?php\n"; ?>

namespace App\Shared\Application\Transaction;

final class Transactionally
{
    /** @param callable():mixed $fn */
    public function __invoke(callable $fn): mixed
    {
        return $fn();
    }
}
