<?php echo "<?php\n"; ?>

namespace App\Contexts\<?php echo $bounded_context; ?>\Interface\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Contexts\<?php echo $bounded_context; ?>\Application\Query\GetSample\GetSampleQuery;

#[Route(path: <?php echo var_export('/'.$slug.'/samples/{id}', true); ?>, name: <?php echo var_export($routePrefix.'_samples_get', true); ?>, methods: ['GET'])]
final class GetSampleGetController
{
    public function __invoke(string $id, QueryBus $bus): JsonResponse
    {
        $dto = $bus->ask(new GetSampleQuery($id));
        if (!$dto) {
            return new JsonResponse(['error' => 'not found'], 404);
        }
        return new JsonResponse(['id' => $dto->id, 'label' => $dto->label]);
    }
}
