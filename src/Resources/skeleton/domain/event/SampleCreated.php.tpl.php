<?php echo "<?php\n"; ?>

namespace App\Contexts\<?php echo $bounded_context; ?>\Domain\Event;

use App\Shared\Domain\Bus\Event\Event;

final class SampleCreated implements Event
{
    public function __construct(
    private string $id,
    private \DateTimeImmutable $occurredOn = new \DateTimeImmutable()
    ) {}

    public function aggregateId(): string { return $this->id; }
    public function occurredOn(): \DateTimeImmutable { return $this->occurredOn; }
}
