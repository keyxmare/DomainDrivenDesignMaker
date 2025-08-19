<?php echo "<?php\n"; ?>

namespace App\Shared\Domain\Bus\Event;

interface EventBus
{
    public function publish(Event ...$events): void;
}
