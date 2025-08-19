<?php echo "<?php\n"; ?>

namespace App\Shared\Domain\Bus\Event;

interface Event
{
    public function aggregateId(): string;
    public function occurredOn(): \DateTimeImmutable;
}
