<?php echo "<?php\n"; ?>

namespace App\Contexts\<?php echo $bounded_context; ?>\Domain\Model;

final class Sample
{
    public function __construct(
    private SampleId $id,
    private string $label
    ) {}

    public static function create(SampleId $id, string $label): self
    {
        return new self($id, $label);
    }

    public function id(): SampleId { return $this->id; }
    public function label(): string { return $this->label; }
    public function rename(string $label): void { $this->label = $label; }
}
