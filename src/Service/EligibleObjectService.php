<?php

namespace App\Service;

use App\Enum\ObjectType;
use App\Enum\Theme;
use App\Http\Client;
use App\Provider\UiConfigProvider;
use App\Trait\DateTrait;
use DateMalformedStringException;
use Drenso\OidcBundle\Exception\OidcException;
use LogicException;
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
final readonly class EligibleObjectService implements ServiceInterface
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
    public function find(Request $request): array
    {
        // extract parameters from request
        $parameters = $this->extractParameters($request);

        // check if the theme is valid
        if (!$this->uiConfigProvider->isThemeValid($parameters['theme'])) {
            throw new LogicException('Unsupported theme: ' . $parameters['theme']);
        }

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
            return $this->buildEligibleContributionResults($results, $objects);
        }

        if ($parameters['theme'] === Theme::PRESTATIONS_SANTE_ELIGIBLE->name) {
            return $this->buildHealthBenefitResults($results, $objects);
        }

        if ($parameters['theme'] === Theme::PRESTATIONS_PREVOYANCE_ELIGIBLE->name) {
            return $this->buildDisabilityBenefitResults($results, $objects);
        }

        if ($parameters['theme'] === Theme::CONTRATS_OPTIONS_LIEN_SALARIAL_ELIGIBLE->name) {
            return $this->buildContractResults($results, $objects);
        }

        if ($parameters['theme'] === Theme::INDIVIDUS_ELIGIBLE->name) {
            return $this->buildIndividualResults($results, $objects);
        }
    }

    /**
     * Default results
     *
     * @param array $results
     * @param array $eligibleContributionObjects
     * @return array
     */
    private function buildEligibleContributionResults(array $results, array $eligibleContributionObjects): array
    {
        foreach ($results['content'] as $key => $result) {

            $eligibleContributionObjects['eligibleObjects'][$key] = [
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
                    'socialSecurityNumber' => $result['numeroSecuriteSociale'] ?? null
                ]
            ];
        }

        return $eligibleContributionObjects;
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
                'membershipNumber' => $result['numAdherent'] ?? null,
                'environment' => $result['environnement'] ?? null,
                'openFileNumber' => $result['numeroDossierOpen'] ?? null,
                'thirdTypeLabel' => $result['libelleTypeTiers'] ?? null,
                'healthBenefitPaymentDate' => !empty($result['datePaiementPrestation']) ? $this->formatDate($result['datePaiementPrestation'], 'Y-m-d', 'd/m/Y') : null,
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
                    'paymentMode' => $result['modePaiement'] ?? null

                ]
            ];
        }

        return $healthBenefitObjects;
    }

    /**
     * Build disability benefit
     *
     * @param array $results
     * @param array $disabilityBenefitObjects
     * @return array
     */
    private function buildDisabilityBenefitResults(array $results, array $disabilityBenefitObjects): array
    {
        foreach ($results['content'] as $key => $result) {

            $disabilityBenefitObjects['eligibleObjects'][$key] = [
                'key' => $key,
                'membershipNumber' => $result['numAdherent'] ?? null,
                'claimNumber' => $result['numeroSinistre'] ?? null,
                'healthBenefitTypology' => $result['typeVersement'] ?? null,
                'claimClosingDate' => !empty($result['dateClotureSinistre']) ? $this->formatDate($result['dateClotureSinistre']) : null,
                'healthBenefitPaymentType' => $result['typePaiement'] ?? null,
                'lastPaymentDate' => !empty($result['datePaiementPrestation']) ? $this->formatDate($result['datePaiementPrestation']) : null,
                'beneficiaryDeathDate' => $result['dateDecesBeneficiaire'] ?? null,
                'conservationTime' => $result['delaiConservation'] ?? null,
                'settingsDescription' => $result['descriptionParametrage'] ?? null,
                'details' => [
                    // beneficiary details
                    'beneficiaryName' => $result['nomBeneficiaire'] ?? null,
                    'beneficiaryFirstname' => $result['prenomBeneficiaire'] ?? null,
                    'beneficiaryBirthdate' => !empty($result['dateNaissanceBeneficiaire']) ? $this->formatDate($result['dateNaissanceBeneficiaire']) : null,
                    'socialSecurityNumber' => $result['numeroSecuriteSociale'] ?? null,
                    'beneficiaryRank' => $result['rangBeneficiaire'] ?? null
                ]
            ];
        }

        return $disabilityBenefitObjects;
    }

    /**
     * Build contracts
     *
     * @param array $results
     * @param array $contractObjects
     * @return array
     */
    private function buildContractResults(array $results, array $contractObjects): array
    {
        foreach ($results['content'] as $key => $result) {

            $contractObjects['eligibleObjects'][$key] = [
                'key' => $key,
                'membershipNumber' => $result['numAdherent'] ?? null,
                'openContractIdentifier' => $result[''] ?? null,
                'contractType' => $result[''] ?? null,
                'subcontractType' => $result[''] ?? null,
                'optionTop' => $result[''] ?? null,
                'cancellingContractDate' => !empty($result['']) ? $this->formatDate($result['']) : null,
                'cancellingContractReason' => $result[''] ?? null,
                'conservationTime' => $result['delaiConservation'] ?? null,
                'settingsDescription' => $result['descriptionParametrage'] ?? null,
                'details' => [
                    // beneficiary details
                    'beneficiaryName' => $result['nomBeneficiaire'] ?? null,
                    'beneficiaryFirstname' => $result['prenomBeneficiaire'] ?? null,
                    'beneficiaryBirthdate' => !empty($result['dateNaissanceBeneficiaire']) ? $this->formatDate($result['dateNaissanceBeneficiaire']) : null,
                    'socialSecurityNumber' => $result['numeroSecuriteSociale'] ?? null
                ]
            ];
        }

        return $contractObjects;
    }

    /**
     * Build individuals
     *
     * @param array $results
     * @param array $individualObjects
     * @return array
     */
    private function buildIndividualResults(array $results, array $individualObjects): array
    {
        foreach ($results['content'] as $key => $result) {
            $individualObjects['eligibleObjects'][$key] = [
                'key' => $key,
                'familyLink' => $result[''] ?? null,
                'membershipNumber' => $result['numAdherent'] ?? null,
                'conservationTime' => $result['delaiConservation'] ?? null,
                'settingsDescription' => $result['descriptionParametrage'] ?? null,
                'details' => [
                    'beneficiaryName' => $result['nomBeneficiaire'] ?? null,
                    'beneficiaryFirstname' => $result['prenomBeneficiaire'] ?? null,
                    'beneficiaryBirthdate' => !empty($result['dateNaissanceBeneficiaire']) ? $this->formatDate($result['dateNaissanceBeneficiaire']) : null,
                    'socialSecurityNumber' => $result['numeroSecuriteSociale'] ?? null
                ]
            ];
        }

        return $individualObjects;
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
    public function extractParameters(Request $request, bool $withPagination = true): array
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

        if (!empty($request->get('membershipNumber'))) {
            $parameters['numAdherent'] = $request->get('membershipNumber');
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
    public function findToExport(Request $request): array
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