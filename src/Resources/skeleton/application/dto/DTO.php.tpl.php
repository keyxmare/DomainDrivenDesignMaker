<?php echo "<?php\n"; ?>

namespace App\Contexts\<?php echo $bounded_context; ?>\Application\DTO;

final class SampleDTO
{
    public function __construct(
    public string $id,
    public string $label
    ) {}

    public static function fromDomain(\App\Contexts\<?php echo $bounded_context; ?>\Domain\Model\Sample $s): self
    {
        return new self((string)$s->id(), $s->label());
    }
}
