<?php

namespace App\Service;

use App\Http\Client;
use App\Trait\DateTrait;
use Drenso\OidcBundle\Exception\OidcException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class EligibleObjectService
 *
 * @package App\Service
 * @category
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license
 * @copyright GFP Tech 2025
 */
class EligibleObjectService
{
    use DateTrait;

    /**
     * English to French mapping
     *
     * @var array|string[]
     */
    private array $fieldsMapping = [
        'campaignDate' => 'dateCampagne',
        'clientName' => 'nomDuClient',
        'environment' => 'environnement',
        'familyId' => 'identifiantFamille',
        'contributionPaymentDate' => 'datePaiementCotisation',
        'contributionCallPeriod' => 'periodeAppelCotisation',
        'contributionCallYear' => 'anneeAppelCotisation',
        'conservationTime' => 'delaiConservation',
        'purgeRuleLabel' => 'libRegPurg',
        'beneficiaryName' => 'nomBeneficiaire',
        'beneficiaryFirstname'  => 'prenomBeneficiaire',
        'beneficiaryBirthdate'  => 'dateNaissanceBeneficiaire',
        'socialSecurityNumber' => 'numeroSecuriteSociale'
    ];

    public function __construct(private readonly Client $client, private readonly string $baseUrl, private readonly LoggerInterface $logger)
    {
    }

    /**
     * Find eligible objects based on criteria
     *
     * @param array $criteria
     * @return array
     * @throws OidcException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function findEligibleObjects(array $criteria): array
    {
        $responseContent = $this->client->doRequest($this->baseUrl . '/api-rgpd/v1/eligibles', $criteria, Request::METHOD_POST);

        $results = json_decode($responseContent, true);

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
                'contributionPaymentDate' => !empty($result['datePaiementCotisation']) ? $this->formatDate($result['datePaiementCotisation'], 'Y-m-d', 'd/m/Y') : null,
                'contributionCallPeriod' => $result['periodeAppelCotisation'] ?? null,
                'contributionCallYear' => $result['anneeAppelCotisation'] ?? null,
                'conservationTime' => $result['delaiConservation'] ?? null,
                'purgeRuleLabel' => $result['libRegPurg'] ?? null,
                'details' => [
                    'key' => $key,
                    'beneficiaryName' => $result['nomBeneficiaire'] ?? null,
                    'beneficiaryFirstname' => $result['prenomBeneficiaire'] ?? null,
                    'beneficiaryBirthdate' => !empty($result['dateNaissanceBeneficiaire']) ? $this->formatDate($result['dateNaissanceBeneficiaire'], 'Y-m-d', 'd/m/Y') : null,
                    'socialSecurityNumber' => $result['numeroSecuriteSociale'] ?? null,

                ]
            ];
        }

        return $eligibleObjects;

    }

    /**
     * @param string $fieldName
     * @return string
     */
    public function convertFieldName(string $fieldName): string
    {
        if (!array_key_exists($fieldName, $this->fieldsMapping)) {
            return $fieldName;
        }

        return $this->fieldsMapping[$fieldName];
    }
}