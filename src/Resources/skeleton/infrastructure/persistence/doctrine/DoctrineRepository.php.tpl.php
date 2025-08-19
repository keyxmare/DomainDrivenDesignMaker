<?php echo "<?php\n"; ?>

namespace App\Contexts\<?php echo $bounded_context; ?>\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use App\Contexts\<?php echo $bounded_context; ?>\Domain\Model\<?php echo $entity; ?>;
use App\Contexts\<?php echo $bounded_context; ?>\Domain\Model\<?php echo $entity; ?>Id;
use App\Contexts\<?php echo $bounded_context; ?>\Domain\Repository\<?php echo $entity; ?>Repository;

final class Doctrine<?php echo $entity; ?>Repository implements <?php echo $entity; ?>Repository
{
    public function __construct(private EntityManagerInterface $em) {}
    public function byId(<?php echo $entity; ?>Id $id): ?<?php echo $entity; ?> { return $this->em->find(<?php echo $entity; ?>::class, $id); }
    public function save(<?php echo $entity; ?> $entity): void { $this->em->persist($entity); }
}
