<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\EligibleObjectService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[Route('/api')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
final class EligibleObjectController extends AbstractController
{
    public function __construct(private readonly EligibleObjectService $eligibleObjectService, private readonly LoggerInterface $logger)
    {
    }

    #[Route('/eligible-object', name: 'api.eligible-object', methods: ['POST'])]
    public function findEligibleObjects(Request $request): JsonResponse
    {
        $jsonResponse = new JsonResponse();

        try {
            $jsonResponse->setData(
                $this->eligibleObjectService->findEligibleObjects([
                    //'environment' => $request->get('environment'),
                    'environnement' => 'MERCERW2', // TODO remove
                    'theme' => $request->get('theme')
                ])
            );
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            $jsonResponse->setData([
                'error' => $e->getMessage()
            ]);
        }

        return $jsonResponse;
    }
}
