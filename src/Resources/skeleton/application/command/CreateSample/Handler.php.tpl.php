<?php echo "<?php\n"; ?>

namespace App\Contexts\<?php echo $bounded_context; ?>\Application\Command\CreateSample;

use App\Contexts\<?php echo $bounded_context; ?>\Domain\Model\Sample;
use App\Contexts\<?php echo $bounded_context; ?>\Domain\Model\SampleId;
use App\Contexts\<?php echo $bounded_context; ?>\Domain\Repository\SampleRepository;
use App\Contexts\<?php echo $bounded_context; ?>\Domain\Event\SampleCreated;
use App\Shared\Domain\Bus\Event\EventBus;

final class CreateSampleHandler
{
    public function __construct(
        private SampleRepository $repo,
        private EventBus $events
    ) {}

    public function __invoke(CreateSampleCommand $cmd): void
    {
        $sample = Sample::create(SampleId::fromString($cmd->id), $cmd->label);
        $this->repo->save($sample);

        $this->events->publish(new SampleCreated((string) $sample->id()));
    }
}
