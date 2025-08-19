<?php echo "<?php\n"; ?>

namespace App\Shared\Infrastructure\Time;

use App\Shared\Domain\Clock\Clock;

final class SystemClock implements Clock
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
