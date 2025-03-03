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
     * @param Request $request
     * @return array
     * @throws ClientExceptionInterface
     * @throws OidcException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function findEligibleObjects(Request $request): array
    {
        $parameters = $this->extractParameters($request, true);

        $responseContent = $this->client->doRequest($this->baseUrl . '/api-rgpd/v1/eligibles', $parameters, Request::METHOD_POST)['content'];

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
     * Convert field name from
     *
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

    /**
     * Extract parameters from request
     *
     * @param Request $request
     * @param $withPagination
     *
     * @return array
     */
    private function extractParameters(Request $request, $withPagination = true): array
    {
        $parameters = [
            //'environment' => $request->get('environment'),
            'environnement' => 'MERCERW2', // TODO remove
            'theme' => $request->get('theme'),
        ];

        if ($withPagination) {
            $parameters['pageable'] = [
                'page' => $request->get('page') ?? 0,
                'size' => 10
            ];
        }

        if (!empty($request->get('sortOrder'))) {
            $parameters['pageable']['sort'][0]['propertie'] = $this->convertFieldName($request->get('sortField'));
            $parameters['pageable']['sort'][0]['direction'] = '-1' === $request->get('sortOrder') ? 'DESC' : 'ASC';
        }

        if (!empty($request->get('dateFrom'))) {
            $parameters['debutPeriode'] = $this->convertDateFromString($request->get('dateFrom'));
        }

        if (!empty($request->get('dateTo'))) {
            $parameters['finPeriode'] = $this->convertDateFromString($request->get('dateTo'));
        }

        if (!empty($request->get('familyId'))) {
            $parameters['identifiantFamille'] = $request->get('familyId');
        }

        return $parameters;
    }

    /**
     * Find eligible objects to export
     *
     * @param Request $request
     * @return array
     * @throws ClientExceptionInterface
     * @throws OidcException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function findEligibleObjectsToExport(Request $request): array
    {
        $parameters = $this->extractParameters($request, false);

        return $this->client->doRequest($this->baseUrl . '/api-rgpd/v1/exporter/eligibles', $parameters, Request::METHOD_POST);

    }

    /**
     * Export data to file and return path
     *
     * @param string $content
     * @param array $headers
     *
     * @return array
     */
    public function makeExport(string $content, array $headers) : array
    {
        $fileName = 'eligible_objects_' . date('Y-m-d') . '.zip';
        if ('text/csv' === $headers['content-type']) {
            $fileName = 'eligible_objects_' . date('Y-m-d') . '.csv';
        }

        $filePath = sys_get_temp_dir() . '/' . $fileName;

        // save file
        file_put_contents($filePath, $content);

        return [
            $filePath,
            $fileName
        ];
    }

}