<?php echo "<?php\n"; ?>

namespace App\Contexts\<?php echo $bounded_context; ?>\Infrastructure\Persistence\InMemory;

use App\Contexts\<?php echo $bounded_context; ?>\Domain\Model\Sample;
use App\Contexts\<?php echo $bounded_context; ?>\Domain\Model\SampleId;
use App\Contexts\<?php echo $bounded_context; ?>\Domain\Repository\SampleRepository;

final class InMemorySampleRepository implements SampleRepository
{
    /** @var array<string, Sample> */
    private array $data = [];

    public function byId(SampleId $id): ?Sample
    {
        return $this->data[(string)$id] ?? null;
    }

    public function save(Sample $sample): void
    {
        $this->data[(string)$sample->id()] = $sample;
    }
}
