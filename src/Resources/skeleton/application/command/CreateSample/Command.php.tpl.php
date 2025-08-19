<?php echo "<?php\n"; ?>

namespace App\Contexts\<?php echo $bounded_context; ?>\Application\Command\CreateSample;

use App\Shared\Domain\Bus\Command\Command;

final class CreateSampleCommand implements Command
{
    public function __construct(
        public string $id,
        public string $label
    ) {}
}
