<?php echo "<?php\n"; ?>

namespace App\Contexts\<?php echo $bounded_context; ?>\Application\Query\GetSample;

use App\Contexts\<?php echo $bounded_context; ?>\Domain\Model\SampleId;
use App\Contexts\<?php echo $bounded_context; ?>\Domain\Repository\SampleRepository;
use App\Contexts\<?php echo $bounded_context; ?>\Application\DTO\SampleDTO;

final class GetSampleHandler
{
    public function __invoke(GetSampleQuery $qry): ?SampleDTO
    {
        $sample = $this->repo->byId(SampleId::fromString($qry->id));
        return $sample ? SampleDTO::fromDomain($sample) : null;
    }

    public function __construct(private SampleRepository $repo) {}
}
