<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\EligibleObjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class EligibleObjectController extends AbstractController
{
    public function __construct(private readonly EligibleObjectService $eligibleObjectService)
    {
    }

    #[Route('/eligible-object', name: 'api.eligible-object', methods: ['POST'])]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'eligiblesObjects' => $this->eligibleObjectService->findEligibleObjects([])
        ]);
    }
}
