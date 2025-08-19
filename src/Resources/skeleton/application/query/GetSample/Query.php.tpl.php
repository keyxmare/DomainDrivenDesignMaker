<?php echo "<?php\n"; ?>

namespace App\Contexts\<?php echo $bounded_context; ?>\Application\Query\GetSample;

use App\Shared\Domain\Bus\Query\Query;

final class GetSampleQuery implements Query
{
    public function __construct(public string $id) {}
}
