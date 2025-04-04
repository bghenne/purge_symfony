<?php

namespace App\Service;

use App\Enum\ObjectType;
use App\Enum\Theme;
use App\Http\Client;
use App\Provider\UiConfigProvider;
use App\Trait\DateTrait;
use DateMalformedStringException;
use Drenso\OidcBundle\Exception\OidcException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
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
final readonly class EligibleObjectService
{
    use DateTrait;

    public function __construct(
        private Client           $client,
        private string           $baseUrl,
        private UiConfigProvider $uiConfigProvider,
        private LoggerInterface  $logger,
        private DecoderInterface $jsonDecoder
    )
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
     * @throws DateMalformedStringException
     */
    public function findEligibleObjects(Request $request): array
    {
        // extract parameters from request
        $parameters = $this->extractParameters($request, true);
        $this->logger->warning(var_export($parameters, true));

        $response = $this->client->doRequest($this->baseUrl . '/api-rgpd/v1/eligibles', $parameters, Request::METHOD_POST);

        $results = $this->jsonDecoder->decode($response['content'], JsonEncoder::FORMAT);

        // this 'content' is the one returned by doRequest()
        if (empty($results['content'])) {
            return ['eligibleObjects' => []]; // if empty content is provided, we have to build this array structure to provide to DataTable component
        }

        $objects = [
            'columns' => [
                'labels' => $this->uiConfigProvider->getPropertyLabels(ObjectType::ELIGIBLE, $parameters['theme']),
                'config' => $this->uiConfigProvider->getColumnsConfig(ObjectType::ELIGIBLE, $parameters['theme']),
            ],
            'advancedSearch' => $this->uiConfigProvider->getAdvancedSearchConfig(ObjectType::ELIGIBLE, $parameters['theme']),
            'eligibleObjects' => [],
            'total' => $results['page']['totalElements']
        ];

        if ($parameters['theme'] === Theme::COTISATIONS_ELIGIBLE->name) {
            return $this->buildEligibleObjectsResults($results, $objects);
        }

        if ($parameters['theme'] === Theme::PRESTATIONS_SANTE_ELIGIBLE->name) {
            return $this->buildHealthBenefitResults($results, $objects);
        }

        if ($parameters['theme'] === Theme::PRESTATIONS_PREVOYANCE_ELIGIBLE->name) {

        }

        if ($parameters['theme'] === Theme::CONTRATS_OPTIONS_LIEN_SALARIAL_ELIGIBLE->name) {

        }

        if ($parameters['theme'] === Theme::INDIVIDUS_ELIGIBLE->name) {

        }
    }

    /**
     * Default results
     *
     * @param array $results
     * @param array $eligibleObjects
     * @return array
     */
    private function buildEligibleObjectsResults(array $results, array $eligibleObjects): array
    {
        foreach ($results['content'] as $key => $result) {

            $eligibleObjects['eligibleObjects'][$key] = [
                'key' => $key,
                'campaignDate' => $this->formatDate($result['dateCampagne'], 'Y-m-d', 'd/m/Y') ?? null,
                'clientName' => $result['nomDuClient'] ?? null,
                'environment' => $result['environnement'] ?? null,
                'membershipNumber' => $result['numAdherent'] ?? null,
                'contributionPaymentDate' => !empty($result['datePaiementCotisation']) ? $this->formatDate($result['datePaiementCotisation'], 'Y-m-d', 'd/m/Y') : null,
                'contributionCallPeriod' => $result['periodeAppelCotisation'] ?? null,
                'contributionCallYear' => $result['anneeAppelCotisation'] ?? null,
                'conservationTime' => $result['delaiConservation'] ?? null,
                'purgeRuleLabel' => $result['libRegPurg'] ?? null,
                'details' => [
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
     * Build health benefit specific
     *
     * @param array $results
     * @param array $healthBenefitObjects
     * @return array
     */
    private function buildHealthBenefitResults(array $results, array $healthBenefitObjects): array
    {
        foreach ($results['content'] as $key => $result) {

            $healthBenefitObjects['eligibleObjects'][$key] = [
                'key' => $key,
                'familyId' => $result['identifiantFamille'] ?? null,
                'openFileNumber' => $result['numeroDossierOpen'] ?? null,
                'thirdTypeLabel' => $result['libelleTypeTiers'] ?? null,
                'healthBenefitPaymentDate' => $result['datePaiementPrestation'] ?? null,
                'conservationTime' => $result['delaiConservation'] ?? null,
                'settingDescription' => $result['descriptionParametrage'] ?? null,
                'details' => [
                    // beneficiary details
                    'beneficiaryName' => $result['nomBeneficiaire'] ?? null,
                    'beneficiaryFirstname' => $result['prenomBeneficiaire'] ?? null,
                    'beneficiaryBirthdate' => !empty($result['dateNaissanceBeneficiaire']) ? $this->formatDate($result['dateNaissanceBeneficiaire'], 'Y-m-d', 'd/m/Y') : null,
                    'socialSecurityNumber' => $result['numeroSecuriteSociale'] ?? null,
                    // health benefit details
                    'finessNumber' => $result['numeroFiness'],
                    'paymentNumber' => $result['numeroPaiement'] ?? null,
                    'paymentType' => $result['typePaiement'] ?? null,
                    'paymentMode' => $result['modePaiement'] ?? null,

                ]
            ];
        }

        return $healthBenefitObjects;
    }

    /**
     * Extract parameters from request
     *
     * @param Request $request
     * @param true $withPagination
     *
     * @return array
     * @throws DateMalformedStringException
     */
    private function extractParameters(Request $request, bool $withPagination = true): array
    {
        $theme = $request->get('theme');
        $parameters = [
            'environnement' => $request->get('environment'),
            'theme' => $theme,
        ];

        if ($withPagination) {
            $parameters['pageable'] = [
                'page' => $request->get('page') ?? 0,
                'size' => 10
            ];
        }

        if (!empty($request->get('sortOrder'))) {
            $parameters['pageable']['sort'][0]['property'] = $this->uiConfigProvider->convertFieldName(ObjectType::ELIGIBLE, $theme, $request->get('sortField'));
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
     * @throws DateMalformedStringException
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
    public function makeExport(string $content, array $headers): array
    {
        $fileName = 'eligible_objects_' . date('Y-m-d') . '.zip';
        if ('text/csv' === $headers['content-type'][0]) {
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