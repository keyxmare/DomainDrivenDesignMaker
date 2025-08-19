<?php echo "<?php\n"; ?>

namespace App\Contexts\<?php echo $bounded_context; ?>\Domain\Repository;

use App\Contexts\<?php echo $bounded_context; ?>\Domain\Model\Sample;
use App\Contexts\<?php echo $bounded_context; ?>\Domain\Model\SampleId;

interface SampleRepository
{
    public function byId(SampleId $id): ?Sample;
    public function save(Sample $sample): void;
}
