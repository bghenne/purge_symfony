<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\EligibleObjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[Route('/api')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
class EligibleObjectController extends AbstractController
{
    public function __construct(private readonly EligibleObjectService $eligibleObjectService)
    {
    }

    #[Route('/eligible-object', name: 'api.eligible-object', methods: ['POST'])]
    public function index(): JsonResponse
    {
        $jsonResponse = new JsonResponse();

        // retrieve parameters from ajax call

        try {
            $jsonResponse->setData([
                'eligiblesObjects' => $this->eligibleObjectService->findEligibleObjects([])
            ]);
        } catch (Throwable $e) {
            $jsonResponse->setData([
                'error' => $e->getMessage()
            ]);
        }

        return $jsonResponse;
    }
}
