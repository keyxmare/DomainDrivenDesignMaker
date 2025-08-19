<?php echo "<?php\n"; ?>

namespace App\Contexts\<?php echo $bounded_context; ?>\Interface\Http\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Contexts\<?php echo $bounded_context; ?>\Application\Command\CreateSample\CreateSampleCommand;

#[Route(path: <?php echo var_export('/'.$slug.'/samples', true); ?>, name: <?php echo var_export($routePrefix.'_samples_create', true); ?>, methods: ['POST'])]
final class CreateSamplePostController
{
    public function __invoke(Request $request, CommandBus $bus): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $id    = $data['id']    ?? null;
        $label = $data['label'] ?? null;
        if (!$id || !$label) {
            return new JsonResponse(['error' => 'id & label required'], 400);
        }

        $bus->dispatch(new CreateSampleCommand($id, $label));
        return new JsonResponse(null, 202);
    }
}
