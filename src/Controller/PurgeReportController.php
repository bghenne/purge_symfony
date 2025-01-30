<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\PurgeReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class PurgeReportController extends AbstractController
{
    public function __construct(private readonly PurgeReportService $purgeReportService)
    {
    }

    #[Route('/purge-report', name: 'api.purge-report', methods: ['POST'])]
    public function index(): JsonResponse
    {
        return new JsonResponse();
    }
}
