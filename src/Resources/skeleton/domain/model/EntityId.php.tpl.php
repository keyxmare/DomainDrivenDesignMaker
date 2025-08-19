<?php echo "<?php\n"; ?>

namespace App\Contexts\<?php echo $bounded_context; ?>\Domain\Model;

final class SampleId
{
    private function __construct(private string $value) {}
    public static function fromString(string $value): self { return new self($value); }
    public function __toString(): string { return $this->value; }
}
