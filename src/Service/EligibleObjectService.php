<?php

namespace App\Service;

use App\Http\Client;
use App\Trait\DateTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EligibleObjectService
 *
 * @package App\Service
 * @category
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license
 * @copyright GFP Tech 2025
 */
readonly class EligibleObjectService
{
    use DateTrait;

    public function __construct(private Client $client, private readonly string $baseUrl, private LoggerInterface $logger)
    {
    }

    /**
     * Find eligible objects based on criteria
     *
     * @return array
     */
    public function findEligibleObjects(array $criteria): array
    {
        $responseContent = $this->client->doRequest($this->baseUrl . '/api-rgpd/v1/eligibles', $criteria, Request::METHOD_POST);

        $results = json_decode($responseContent, true);

        //$this->logger->warning(var_export($results, true));
        $eligibleObjects = [
            'eligibleObjects' => [],
            'total' => $results['page']['totalElements']
        ];

        foreach ($results['content'] as $key => $result) {

            $eligibleObjects['eligibleObjects'][$key] = [
                'key' => $key,
                'campaignDate' => $this->formatDate($result['dateCampagne'], 'Y-m-d', 'd/m/Y') ?? null,
                'clientName' => $result['nomDuClient'] ?? null,
                'environment' => $result['environnement'] ?? null,
                'familyId' => $result['identifiantFamille'] ?? null,
                'beneficiaryName' => $result['nomBeneficiaire'] ?? null,
                'beneficiaryFirstname' => $result['prenomBeneficiaire'] ?? null,
                'beneficiaryBirthdate' => !empty($result['dateNaissanceBeneficiaire']) ? $this->formatDate($result['dateNaissanceBeneficiaire'], 'Y-m-d', 'd/m/Y') : null,
                'socialSecurityNumber' => $result['numeroSecuriteSociale'] ?? null,
                'details' => [
                    'key' => $key,
                    'contributionPaymentDate' => !empty($result['datePaiementCotisation']) ? $this->formatDate($result['datePaiementCotisation'], 'Y-m-d', 'd/m/Y') : null,
                    'contributionCallPeriod' => $result['periodeAppelCotisation'] ?? null,
                    'contributionCallYear' => $result['anneeAppelCotisation'] ?? null,
                    'conservationTime' => $result['delaiConservation'] ?? null,
                    'purgeRuleLabel' => $result['libRegPurg'] ?? null,
                ]
            ];
        }

        return $eligibleObjects;

    }
}