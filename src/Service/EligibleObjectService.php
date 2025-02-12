<?php

namespace App\Service;

use App\Http\Client;
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
    public function __construct(private Client $client, private readonly string $baseUrl)
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

        $results = json_decode($responseContent, true)['content'];
        $eligibleObjects = [];

        foreach ($results as $key => $result) {

            $eligibleObjects[$key] = [
                'key' => $key,
                'campaignDate' => $result['dateCampagne'] ?? null,
                'clientName' => $result['nomDuClient'] ?? null,
                'environment' => $result['environnement'] ?? null,
                'familyId' => $result['identifiantFamille'] ?? null,
                'beneficiaryName' => $result['nomBeneficiaire'] ?? null,
                'beneficiaryFirstname' => $result['prenomBeneficiaire'] ?? null,
                'beneficiaryBirthdate' => $result['dateNaissanceBeneficiaire'] ?? null,
                'socialSecurityNumber' => $result['numeroSecuriteSociale'] ?? null,
                'details' => [
                    'key' => $key,
                    'contributionPaymentDate' => $result['datePaiementCotisation'] ?? null,
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