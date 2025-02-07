<?php

namespace App\Controller;

use App\Service\ThemeService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[Route('/api')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
final class ThemeController extends AbstractController
{
    public function __construct(private readonly ThemeService $themeService, private readonly LoggerInterface $logger)
    {
    }

    #[Route('/theme/{objectType}', name: 'api.theme')]
    #[IsGranted('ROLE_USER')]
    public function getThemesByObjectType(string $objectType): JsonResponse
    {

        $jsonResponse = new JsonResponse();

        try {

            $jsonResponse->setData($this->themeService->findThemesByObjectType($objectType));

        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            $jsonResponse->setData([
                'error' => $e->getMessage()
            ]);
        }

        return $jsonResponse;
    }
}
