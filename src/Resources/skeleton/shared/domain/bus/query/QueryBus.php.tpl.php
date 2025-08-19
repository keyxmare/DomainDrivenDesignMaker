<?php echo "<?php\n"; ?>

namespace App\Shared\Domain\Bus\Query;

interface QueryBus
{
    public function ask(Query $query): mixed;
}
