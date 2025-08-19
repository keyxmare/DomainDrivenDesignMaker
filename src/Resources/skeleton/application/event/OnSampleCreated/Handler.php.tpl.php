<?php echo "<?php\n"; ?>

namespace App\Contexts\<?php echo $bounded_context; ?>\Application\Event\OnSampleCreated;

use App\Contexts\<?php echo $bounded_context; ?>\Domain\Event\SampleCreated;
use Psr\Log\LoggerInterface;

final class OnSampleCreatedHandler
{
    public function __construct(private LoggerInterface $logger) {}

    public function __invoke(SampleCreated $event): void
    {
        $this->logger->info('[<?php echo $bounded_context; ?>] SampleCreated', [
            'id' => $event->aggregateId(),
            'at' => $event->occurredOn()->format(DATE_ATOM),
        ]);
    }
}
