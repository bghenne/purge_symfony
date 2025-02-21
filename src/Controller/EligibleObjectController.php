<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\EligibleObjectService;
use App\Trait\DateTrait;
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
    use DateTrait;

    public function __construct(private readonly EligibleObjectService $eligibleObjectService, private readonly LoggerInterface $logger)
    {
    }

    #[Route('/eligible-object', name: 'api.eligible-object', methods: ['POST'])]
    public function findEligibleObjects(Request $request): JsonResponse
    {
        $jsonResponse = new JsonResponse();

        $parameters = [
            //'environment' => $request->get('environment'),
            'environnement' => 'MERCERW2', // TODO remove
            'theme' => $request->get('theme'),
            'pageable' =>  [
                'page' => $request->get('page') ?? 0,
                'size' => 10
            ],
        ];

        if (!empty($request->get('dateFrom'))) {
            $parameters['debutPeriode'] = $this->convertDateFromString($request->get('dateFrom'));
        }

        if (!empty($request->get('dateTo'))) {
            $parameters['finPeriode'] = $this->convertDateFromString($request->get('dateTo'));
        }

        if (!empty($request->get('familyId'))) {
            $parameters['idFass'] = $request->get('familyId');
        }

        try {
            $this->logger->warning(json_encode($parameters));
            $jsonResponse->setData(
                $this->eligibleObjectService->findEligibleObjects($parameters)
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
