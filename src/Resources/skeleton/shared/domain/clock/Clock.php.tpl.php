<?php echo "<?php\n"; ?>

namespace App\Shared\Domain\Clock;

interface Clock
{
    public function now(): \DateTimeImmutable;
}
