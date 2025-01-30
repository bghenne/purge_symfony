<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\PurgedObjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class PurgedObjectController extends AbstractController
{
    public function __construct(private readonly PurgedObjectService $purgedObjectService)
    {
    }

    #[Route('/purged-object', name: 'api.purged-object', methods: ['POST'])]
    public function index(): JsonResponse
    {
        return new JsonResponse();
    }
}
