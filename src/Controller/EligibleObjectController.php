<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\EligibleObjectService;
use App\Trait\DateTrait;
use Drenso\OidcBundle\Exception\OidcException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;

#[Route('/api')]
#[IsGranted("IS_AUTHENTICATED_FULLY")]
final class EligibleObjectController extends AbstractController
{
    use DateTrait;

    public function __construct(private readonly EligibleObjectService $eligibleObjectService, private readonly LoggerInterface $logger)
    {
    }

    #[Route('/eligible-object', name: 'api.eligible-object', methods: ['POST'])]
    public function findEligibleObjects(Request $request): JsonResponse
    {
        $jsonResponse = new JsonResponse();

        try {
            $jsonResponse->setData($this->eligibleObjectService->findEligibleObjects($request));
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            $jsonResponse->setData([
                'error' => $e->getMessage()
            ]);
        }

        return $jsonResponse;
    }

    /**
     * @param Request $request
     * @return BinaryFileResponse|Response
     */
    #[Route('/eligible-object/export', name: 'api.eligible-object.export', methods: ['GET'])]
    public function findEligibleObjectsToExport(Request $request): BinaryFileResponse|Response
    {
        try {

            $result = $this->eligibleObjectService->findEligibleObjectsToExport($request);
            list($filePath, $fileName) = $this->eligibleObjectService->makeExport($result['content'], $result['headers']);

            $binaryFileResponse = new BinaryFileResponse($filePath, Response::HTTP_OK, ['Content-Type' => $result['headers']['content-type']]);
            $binaryFileResponse->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $fileName
            );

            return $binaryFileResponse;

        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            return new Response('File not found !', Response::HTTP_NOT_FOUND);
        }

    }
}
