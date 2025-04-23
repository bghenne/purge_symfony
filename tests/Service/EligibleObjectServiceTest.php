<?php

namespace App\Tests\Service;

use App\Enum\ObjectType;
use App\Http\Client;
use App\Provider\UiConfigProvider;
use App\Service\EligibleObjectService;
use DateMalformedStringException;
use Drenso\OidcBundle\Exception\OidcException;
use LogicException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class EligibleObjectServiceTest
 *
 * @package App\Tests\Service
 * @category
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license
 * @copyright GFP Tech 2025
 */
class EligibleObjectServiceTest extends TestCase
{
    private MockObject $clientMock;

    private MockObject $jsonDecoderMock;

    private MockObject $uiConfigProviderMock;

    private EligibleObjectService $instance;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['doRequest'])
            ->getMock();

        $this->jsonDecoderMock = $this->createMock(DecoderInterface::class);
        $loggerMock = $this->createMock(LoggerInterface::class);

        $this->uiConfigProviderMock = $this->getMockBuilder(UiConfigProvider::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isThemeValid', 'convertFieldName', 'getPropertyLabels', 'getColumnsConfig', 'getAdvancedSearchConfig'])
            ->getMock();

        $this->instance = new EligibleObjectService(
            $this->clientMock,
            '/url',
            $this->uiConfigProviderMock,
            $loggerMock,
            $this->jsonDecoderMock
        );
    }

    /**
     * @return void
     * @throws DateMalformedStringException
     * @throws OidcException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testFindEligibleContributionResults(): void
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $requestMock->expects($this->exactly(12))
            ->method('get')
            ->willReturnCallback(function ($parameter) {
                static $invocationCount = 0;
                $consecutiveArgs = [
                    'theme', 'environment', 'page', 'sortOrder',
                    'sortField', 'sortOrder', 'dateFrom', 'dateFrom',
                    'dateTo', 'dateTo', 'membershipNumber', 'membershipNumber',
                ];
                $consecutiveResults = [
                    'COTISATIONS_ELIGIBLE', 'MERCERWA', 1, '1',
                    'name', '1', '2025-04-01', '2025-04-01',
                    '2025-04-30', '2025-04-30', '12345', '12345'
                ];

                $this->assertEquals($consecutiveArgs[$invocationCount], $parameter);

                return $consecutiveResults[$invocationCount++];
            });

        $this->uiConfigProviderMock->expects($this->once())
            ->method('convertFieldName')
            ->with(ObjectType::ELIGIBLE, 'COTISATIONS_ELIGIBLE', 'name')
            ->willReturn('nom');

        $this->uiConfigProviderMock->expects($this->once())
             ->method('isThemeValid')
             ->with('COTISATIONS_ELIGIBLE')
             ->willReturn(true);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getPropertyLabels')
            ->with(ObjectType::ELIGIBLE, 'COTISATIONS_ELIGIBLE')
            ->willReturn([
                'membershipNumber' => 'Numéro adhérent',
                'contributionPaymentDate' => 'Date de dernier paiement de la cotisation'
            ]);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getColumnsConfig')
            ->with(ObjectType::ELIGIBLE, 'COTISATIONS_ELIGIBLE')
            ->willReturn([
                'membershipNumber' => [
                    'sortable' => false
                ],
                'contributionPaymentDate' => [
                    'sortable' => true
                ]
            ]);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getAdvancedSearchConfig')
            ->with(ObjectType::ELIGIBLE, 'COTISATIONS_ELIGIBLE')
            ->willReturn([
                [
                    'fields' => [
                        [
                            'name' => 'dateFrom',
                            'label' => 'DU',
                            'type' => 'date'
                        ]
                    ],
                    'label' => 'Par date de paiement'
                ]
            ]);

        $this->clientMock->expects($this->once())
            ->method('doRequest')
            ->with('/url/api-rgpd/v1/eligibles', [
                'environnement' => 'MERCERWA',
                'theme' => 'COTISATIONS_ELIGIBLE',
                'pageable' => [
                    'sort' => [
                        [
                            'direction' => 'ASC',
                            'property' => 'nom'
                        ]
                    ],
                    'page' => 1,
                    'size' => 10
                ],
                'debutPeriode' => '2025-04-01',
                'finPeriode' => '2025-04-30',
                'numAdherent' => '12345'
            ], Request::METHOD_POST)
            ->willReturn([
                'content' => '{"content": [
                    {
                            "dateCampagne": "2025-01-01",
                            "nomDuClient": "MERCER",
                            "environnement": "MERCERWA",
                            "identifiantFamille": 5,
                            "numAdherent": "01000005",
                            "nomBeneficiaire": "ASS5",
                            "prenomBeneficiaire": "LOUIS",
                            "dateNaissanceBeneficiaire": "1920-05-18",
                            "numeroSecuriteSociale": "1200578999995",
                            "datePaiementCotisation": "2018-03-30",
                            "periodeAppelCotisation": "T1",
                            "anneeAppelCotisation": 2018,
                            "delaiConservation": 5,
                            "libRegPurg": null
                    }],
                    "page": {
                        "totalElements": 1
                    }
                }',
                'headers' => []
            ]);

        $this->jsonDecoderMock->expects($this->once())
            ->method('decode')
            ->with('{"content": [
                    {
                            "dateCampagne": "2025-01-01",
                            "nomDuClient": "MERCER",
                            "environnement": "MERCERWA",
                            "identifiantFamille": 5,
                            "numAdherent": "01000005",
                            "nomBeneficiaire": "ASS5",
                            "prenomBeneficiaire": "LOUIS",
                            "dateNaissanceBeneficiaire": "1920-05-18",
                            "numeroSecuriteSociale": "1200578999995",
                            "datePaiementCotisation": "2018-03-30",
                            "periodeAppelCotisation": "T1",
                            "anneeAppelCotisation": 2018,
                            "delaiConservation": 5,
                            "libRegPurg": null
                    }],
                    "page": {
                        "totalElements": 1
                    }
                }')
            ->willReturn([
                'content' => [
                    [
                        'dateCampagne' => "2025-01-01",
                        'nomDuClient' => "MERCER",
                        "environnement" => "MERCERWA",
                        "identifiantFamille" => 5,
                        "numAdherent" => "01000005",
                        "nomBeneficiaire" => "ASS5",
                        "prenomBeneficiaire" => "LOUIS",
                        "dateNaissanceBeneficiaire" => "1920-05-18",
                        "numeroSecuriteSociale" => "1200578999995",
                        "datePaiementCotisation" => "2018-03-30",
                        "periodeAppelCotisation" => "T1",
                        "anneeAppelCotisation" => 2018,
                        "delaiConservation" => 5,
                        "libRegPurg" => null
                    ]
                ],
                'page' => [
                    'totalElements' => 1
                ]
            ]);

        $this->assertEquals([
            'columns' => [
                'labels' => [
                    'membershipNumber' => 'Numéro adhérent',
                    'contributionPaymentDate' => 'Date de dernier paiement de la cotisation'
                ],
                'config' => [
                    'membershipNumber' => [
                        'sortable' => false
                    ],
                    'contributionPaymentDate' => [
                        'sortable' => true
                    ]
                ]
            ],
            'advancedSearch' => [
                [
                    'fields' => [
                        [
                            'name' => 'dateFrom',
                            'label' => 'DU',
                            'type' => 'date'
                        ]
                    ],
                    'label' => 'Par date de paiement'
                ]
            ],
            'eligibleObjects' => [[
                'key' => 0,
                'campaignDate' => '01/01/2025',
                'clientName' => 'MERCER',
                'environment' => 'MERCERWA',
                'membershipNumber' => '01000005',
                'contributionPaymentDate' => '30/03/2018',
                'contributionCallPeriod' => 'T1',
                'contributionCallYear' => 2018,
                'conservationTime' => 5,
                'purgeRuleLabel' => null,
                'details' => [
                    'beneficiaryName' => 'ASS5',
                    'beneficiaryFirstname' => 'LOUIS',
                    'beneficiaryBirthdate' => '18/05/1920',
                    'socialSecurityNumber' => '1200578999995'
                ]
            ]],
            'total' => 1
        ],
            $this->instance->find($requestMock)
        );
    }

    /**
     * @return void
     * @throws DateMalformedStringException
     * @throws OidcException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testFindHealthBenefitResults(): void
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $requestMock->expects($this->exactly(12))
            ->method('get')
            ->willReturnCallback(function ($parameter) {
                static $invocationCount = 0;
                $consecutiveArgs = [
                    'theme', 'environment', 'page', 'sortOrder',
                    'sortField', 'sortOrder', 'dateFrom', 'dateFrom',
                    'dateTo', 'dateTo', 'membershipNumber', 'membershipNumber',
                ];
                $consecutiveResults = [
                    'PRESTATIONS_SANTE_ELIGIBLE', 'MERCERWA', 1, '1',
                    'name', '1', '2025-04-01', '2025-04-01',
                    '2025-04-30', '2025-04-30', '12345', '12345'
                ];

                $this->assertEquals($consecutiveArgs[$invocationCount], $parameter);

                return $consecutiveResults[$invocationCount++];
            });

        $this->uiConfigProviderMock->expects($this->once())
            ->method('convertFieldName')
            ->with(ObjectType::ELIGIBLE, 'PRESTATIONS_SANTE_ELIGIBLE', 'name')
            ->willReturn('nom');

        $this->uiConfigProviderMock->expects($this->once())
            ->method('isThemeValid')
            ->with('PRESTATIONS_SANTE_ELIGIBLE')
            ->willReturn(true);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getPropertyLabels')
            ->with(ObjectType::ELIGIBLE, 'PRESTATIONS_SANTE_ELIGIBLE')
            ->willReturn([
                'membershipNumber' => 'Numéro adhérent',
                'conservationTime' => 'Délai de conservation'
            ]);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getColumnsConfig')
            ->with(ObjectType::ELIGIBLE, 'PRESTATIONS_SANTE_ELIGIBLE')
            ->willReturn([
                'membershipNumber' => [
                    'sortable' => false
                ],
                'conservationTime' => [
                    'sortable' => true
                ]
            ]);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getAdvancedSearchConfig')
            ->with(ObjectType::ELIGIBLE, 'PRESTATIONS_SANTE_ELIGIBLE')
            ->willReturn([
                [
                    'fields' => [
                        [
                            'name' => 'dateFrom',
                            'label' => 'DU',
                            'type' => 'date'
                        ]
                    ],
                    'label' => 'Par date de paiement sur une période'
                ]
            ]);

        $this->clientMock->expects($this->once())
            ->method('doRequest')
            ->with('/url/api-rgpd/v1/eligibles', [
                'environnement' => 'MERCERWA',
                'theme' => 'PRESTATIONS_SANTE_ELIGIBLE',
                'pageable' => [
                    'sort' => [
                        [
                            'direction' => 'ASC',
                            'property' => 'nom'
                        ]
                    ],
                    'page' => 1,
                    'size' => 10
                ],
                'debutPeriode' => '2025-04-01',
                'finPeriode' => '2025-04-30',
                'numAdherent' => '12345'
            ], Request::METHOD_POST)
            ->willReturn([
                'content' => '{"content": [
                    {
                            "numAdherent": "01000005",
                            "environnement": "MERCERWA",
                            "numeroDossierOpen": "7700410009411",
                            "libelleTypeTiers": "MERCER",
                            "datePaiementPrestation": "2025-04-01",
                            "delaiConservation": 5,
                            "descriptionParametrage": null,
                            "nomBeneficiaire": "ASS5",
                            "prenomBeneficiaire": "LOUIS",
                            "dateNaissanceBeneficiaire": "1920-05-18",
                            "numeroSecuriteSociale": "1200578999995",
                            "numeroFiness": "0932013840",
                            "numeroPaiement": "0",
                            "typePaiement": "Paiement1",
                            "modePaiement": null
                    }],
                    "page": {
                        "totalElements": 1
                    }
                }',
                'headers' => []
            ]);

        $this->jsonDecoderMock->expects($this->once())
            ->method('decode')
            ->with('{"content": [
                    {
                            "numAdherent": "01000005",
                            "environnement": "MERCERWA",
                            "numeroDossierOpen": "7700410009411",
                            "libelleTypeTiers": "MERCER",
                            "datePaiementPrestation": "2025-04-01",
                            "delaiConservation": 5,
                            "descriptionParametrage": null,
                            "nomBeneficiaire": "ASS5",
                            "prenomBeneficiaire": "LOUIS",
                            "dateNaissanceBeneficiaire": "1920-05-18",
                            "numeroSecuriteSociale": "1200578999995",
                            "numeroFiness": "0932013840",
                            "numeroPaiement": "0",
                            "typePaiement": "Paiement1",
                            "modePaiement": null
                    }],
                    "page": {
                        "totalElements": 1
                    }
                }')
            ->willReturn([
                'content' => [
                    [
                        "numAdherent" => "01000005",
                        "environnement" => "MERCERWA",
                        "numeroDossierOpen" => "7700410009411",
                        "libelleTypeTiers" => "MERCER",
                        "datePaiementPrestation" => "2025-04-01",
                        "delaiConservation" => 5,
                        "descriptionParametrage" => null,
                        "nomBeneficiaire" => "ASS5",
                        "prenomBeneficiaire" => "LOUIS",
                        "dateNaissanceBeneficiaire" => "1920-05-18",
                        "numeroSecuriteSociale" => "1200578999995",
                        "numeroFiness" => "0932013840",
                        "numeroPaiement" => 0,
                        "typePaiement" => "Paiement1",
                        "modePaiement" => null
                    ]
                ],
                'page' => [
                    'totalElements' => 1
                ]
            ]);

        $this->assertEquals([
            'columns' => [
                'labels' => [
                    'membershipNumber' => 'Numéro adhérent',
                    'conservationTime' => 'Délai de conservation'
                ],
                'config' => [
                    'membershipNumber' => [
                        'sortable' => false
                    ],
                    'conservationTime' => [
                        'sortable' => true
                    ]
                ]
            ],
            'advancedSearch' => [
                [
                    'fields' => [
                        [
                            'name' => 'dateFrom',
                            'label' => 'DU',
                            'type' => 'date'
                        ]
                    ],
                    'label' => 'Par date de paiement sur une période'
                ]
            ],
            'eligibleObjects' => [[
                'key' => 0,
                'membershipNumber' => '01000005',
                'environment' => 'MERCERWA',
                'openFileNumber' => '7700410009411',
                'thirdTypeLabel' => 'MERCER',
                'healthBenefitPaymentDate' => '01/04/2025',
                'conservationTime' => 5,
                'settingDescription' => null,
                'details' => [
                    'beneficiaryName' => 'ASS5',
                    'beneficiaryFirstname' => 'LOUIS',
                    'beneficiaryBirthdate' => '18/05/1920',
                    'socialSecurityNumber' => '1200578999995',
                    'finessNumber' => '0932013840',
                    'paymentNumber' => 0,
                    'paymentType' => 'Paiement1',
                    'paymentMode' => null

                ]
            ]],
            'total' => 1
        ],
            $this->instance->find($requestMock)
        );
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws DateMalformedStringException
     * @throws OidcException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testBuildDisabilityBenefitsResults(): void
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $requestMock->expects($this->exactly(12))
            ->method('get')
            ->willReturnCallback(function ($parameter) {
                static $invocationCount = 0;
                $consecutiveArgs = [
                    'theme', 'environment', 'page', 'sortOrder',
                    'sortField', 'sortOrder', 'dateFrom', 'dateFrom',
                    'dateTo', 'dateTo', 'membershipNumber', 'membershipNumber',
                ];
                $consecutiveResults = [
                    'PRESTATIONS_PREVOYANCE_ELIGIBLE', 'MERCERWA', 1, '1',
                    'name', '1', '2025-04-01', '2025-04-01',
                    '2025-04-30', '2025-04-30', '12345', '12345'
                ];

                $this->assertEquals($consecutiveArgs[$invocationCount], $parameter);

                return $consecutiveResults[$invocationCount++];
            });

        $this->uiConfigProviderMock->expects($this->once())
            ->method('convertFieldName')
            ->with(ObjectType::ELIGIBLE, 'PRESTATIONS_PREVOYANCE_ELIGIBLE', 'name')
            ->willReturn('nom');

        $this->uiConfigProviderMock->expects($this->once())
            ->method('isThemeValid')
            ->with('PRESTATIONS_PREVOYANCE_ELIGIBLE')
            ->willReturn(true);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getPropertyLabels')
            ->with(ObjectType::ELIGIBLE, 'PRESTATIONS_PREVOYANCE_ELIGIBLE')
            ->willReturn([
                'membershipNumber' => 'Numéro adhérent',
                'claimNumber' => 'Numéro de sinistre'
            ]);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getColumnsConfig')
            ->with(ObjectType::ELIGIBLE, 'PRESTATIONS_PREVOYANCE_ELIGIBLE')
            ->willReturn([
                'membershipNumber' => [
                    'sortable' => false
                ],
                'claimNumber' => [
                    'sortable' => true
                ]
            ]);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getAdvancedSearchConfig')
            ->with(ObjectType::ELIGIBLE, 'PRESTATIONS_PREVOYANCE_ELIGIBLE')
            ->willReturn([
                [
                    'fields' => [
                        [
                            'name' => 'dateFrom',
                            'label' => 'DU',
                            'type' => 'date'
                        ]
                    ],
                    'label' => 'Par date de paiement sur la période'
                ]
            ]);

        $this->clientMock->expects($this->once())
            ->method('doRequest')
            ->with('/url/api-rgpd/v1/eligibles', [
                'environnement' => 'MERCERWA',
                'theme' => 'PRESTATIONS_PREVOYANCE_ELIGIBLE',
                'pageable' => [
                    'sort' => [
                        [
                            'direction' => 'ASC',
                            'property' => 'nom'
                        ]
                    ],
                    'page' => 1,
                    'size' => 10
                ],
                'debutPeriode' => '2025-04-01',
                'finPeriode' => '2025-04-30',
                'numAdherent' => '12345'
            ], Request::METHOD_POST)
            ->willReturn([
                'content' => '{"content": [
                    {
                            "dateCampagne": "2025-02-01",
                            "nomDuClient": "NOVEO",
                            "environnement": "GFPPREV",
                            "identifiantFamille": 20,
                            "numAdherent": "07000020",
                            "nomBeneficiaire": "ASS20",
                            "prenomBeneficiaire": "CHRISTIAN",
                            "dateNaissanceBeneficiaire": "1960-10-08",
                            "dateDecesBeneficiaire": null,
                            "rangBeneficiaire": 1,
                            "libelleRangBeneficiaire": "",
                            "numeroSecuriteSociale": "1601074999920",
                            "numeroSinistre": 4590021702170,
                            "dateClotureSinistre": "2018-03-21",
                            "typeSinistre": "",
                            "dateDernierPaiement": null,
                            "typeVersement": "",
                            "dateInvalidite": null,
                            "delaiConservation": 5,
                            "parametrage": "PARMPINC1",
                            "descriptionParametrage": "règle date de cloture et date du dernier paiement"
                    }],
                    "page": {
                        "totalElements": 1
                    }
                }',
                'headers' => []
            ]);

        $this->jsonDecoderMock->expects($this->once())
            ->method('decode')
            ->with('{"content": [
                    {
                            "dateCampagne": "2025-02-01",
                            "nomDuClient": "NOVEO",
                            "environnement": "GFPPREV",
                            "identifiantFamille": 20,
                            "numAdherent": "07000020",
                            "nomBeneficiaire": "ASS20",
                            "prenomBeneficiaire": "CHRISTIAN",
                            "dateNaissanceBeneficiaire": "1960-10-08",
                            "dateDecesBeneficiaire": null,
                            "rangBeneficiaire": 1,
                            "libelleRangBeneficiaire": "",
                            "numeroSecuriteSociale": "1601074999920",
                            "numeroSinistre": 4590021702170,
                            "dateClotureSinistre": "2018-03-21",
                            "typeSinistre": "",
                            "dateDernierPaiement": null,
                            "typeVersement": "",
                            "dateInvalidite": null,
                            "delaiConservation": 5,
                            "parametrage": "PARMPINC1",
                            "descriptionParametrage": "règle date de cloture et date du dernier paiement"
                    }],
                    "page": {
                        "totalElements": 1
                    }
                }')
            ->willReturn([
                'content' => [
                    [
                        "dateCampagne" => "2025-02-01",
                        "nomDuClient" => "NOVEO",
                        "environnement" => "GFPPREV",
                        "identifiantFamille" => 20,
                        "numAdherent" => "07000020",
                        "nomBeneficiaire" => "ASS20",
                        "prenomBeneficiaire" => "CHRISTIAN",
                        "dateNaissanceBeneficiaire" => "1960-10-08",
                        "dateDecesBeneficiaire" => null,
                        "rangBeneficiaire" => 1,
                        "libelleRangBeneficiaire" => "",
                        "numeroSecuriteSociale" => "1601074999920",
                        "numeroSinistre" => 4590021702170,
                        "dateClotureSinistre" => "2018-03-21",
                        "typeSinistre" => "",
                        "dateDernierPaiement" => null,
                        "typeVersement" => "",
                        "dateInvalidite" => null,
                        "delaiConservation" => 5,
                        "parametrage" => "PARMPINC1",
                        "descriptionParametrage" => "règle date de cloture et date du dernier paiement"
                    ]
                ],
                'page' => [
                    'totalElements' => 1
                ]
            ]);

        $this->assertEquals([
            'columns' => [
                'labels' => [
                    'membershipNumber' => 'Numéro adhérent',
                    'claimNumber' => 'Numéro de sinistre'
                ],
                'config' => [
                    'membershipNumber' => [
                        'sortable' => false
                    ],
                    'claimNumber' => [
                        'sortable' => true
                    ]
                ]
            ],
            'advancedSearch' => [
                [
                    'fields' => [
                        [
                            'name' => 'dateFrom',
                            'label' => 'DU',
                            'type' => 'date'
                        ]
                    ],
                    'label' => 'Par date de paiement sur la période'
                ]
            ],
            'eligibleObjects' => [[
                'key' => 0,
                'membershipNumber' => '07000020',
                'claimNumber' => 4590021702170,
                'healthBenefitTypology' => '',
                'claimClosingDate' => '21/03/2018',
                'healthBenefitPaymentType' => '',
                'lastPaymentDate' => null,
                'beneficiaryDeathDate' => null,
                'conservationTime' => 5,
                'settingsDescription' => 'règle date de cloture et date du dernier paiement',
                'details' => [
                    'beneficiaryName' => 'ASS20',
                    'beneficiaryFirstname' => 'CHRISTIAN',
                    'beneficiaryBirthdate' => '08/10/1960',
                    'socialSecurityNumber' => '1601074999920',
                    'beneficiaryRank' => 1
                ]
            ]],
            'total' => 1
        ],
            $this->instance->find($requestMock)
        );
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws DateMalformedStringException
     * @throws OidcException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testBuildContractResults(): void
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $requestMock->expects($this->exactly(12))
            ->method('get')
            ->willReturnCallback(function ($parameter) {
                static $invocationCount = 0;
                $consecutiveArgs = [
                    'theme', 'environment', 'page', 'sortOrder',
                    'sortField', 'sortOrder', 'dateFrom', 'dateFrom',
                    'dateTo', 'dateTo', 'membershipNumber', 'membershipNumber',
                ];
                $consecutiveResults = [
                    'CONTRATS_OPTIONS_LIEN_SALARIAL_ELIGIBLE', 'MERCERWA', 1, '1',
                    'name', '1', '2025-04-01', '2025-04-01',
                    '2025-04-30', '2025-04-30', '12345', '12345'
                ];

                $this->assertEquals($consecutiveArgs[$invocationCount], $parameter);

                return $consecutiveResults[$invocationCount++];
            });

        $this->uiConfigProviderMock->expects($this->once())
            ->method('convertFieldName')
            ->with(ObjectType::ELIGIBLE, 'CONTRATS_OPTIONS_LIEN_SALARIAL_ELIGIBLE', 'name')
            ->willReturn('nom');

        $this->uiConfigProviderMock->expects($this->once())
            ->method('isThemeValid')
            ->with('CONTRATS_OPTIONS_LIEN_SALARIAL_ELIGIBLE')
            ->willReturn(true);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getPropertyLabels')
            ->with(ObjectType::ELIGIBLE, 'CONTRATS_OPTIONS_LIEN_SALARIAL_ELIGIBLE')
            ->willReturn([
                'membershipNumber' => 'Numéro adhérent',
                'beneficiaryName' => 'Nom'
            ]);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getColumnsConfig')
            ->with(ObjectType::ELIGIBLE, 'CONTRATS_OPTIONS_LIEN_SALARIAL_ELIGIBLE')
            ->willReturn([
                'membershipNumber' => [
                    'sortable' => true
                ],
                'beneficiaryName' => [
                    'sortable' => false
                ]
            ]);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getAdvancedSearchConfig')
            ->with(ObjectType::ELIGIBLE, 'CONTRATS_OPTIONS_LIEN_SALARIAL_ELIGIBLE')
            ->willReturn([
                [
                    'fields' => [
                        [
                            'emptyOption' => 'Sélection en cours',
                            'name' => 'typology',
                            'type' => 'select',
                            'options' => ["Contrat Santé", "Contrat Prévoyance"]
                        ]
                    ],
                    'label' => 'Par typologie'
                ]
            ]);

        $this->clientMock->expects($this->once())
            ->method('doRequest')
            ->with('/url/api-rgpd/v1/eligibles', [
                'environnement' => 'MERCERWA',
                'theme' => 'CONTRATS_OPTIONS_LIEN_SALARIAL_ELIGIBLE',
                'pageable' => [
                    'sort' => [
                        [
                            'direction' => 'ASC',
                            'property' => 'nom'
                        ]
                    ],
                    'page' => 1,
                    'size' => 10
                ],
                'debutPeriode' => '2025-04-01',
                'finPeriode' => '2025-04-30',
                'numAdherent' => '12345'
            ], Request::METHOD_POST)
            ->willReturn([
                'content' => '{"content": [
                    {
                            "numAdherent": "07000020",
                            "nomBeneficiaire": "ASS20",
                            "prenomBeneficiaire": "CHRISTIAN",
                            "dateNaissanceBeneficiaire": "1960-10-08",
                    }],
                    "page": {
                        "totalElements": 1
                    }
                }',
                'headers' => []
            ]);

        $this->jsonDecoderMock->expects($this->once())
            ->method('decode')
            ->with('{"content": [
                    {
                            "numAdherent": "07000020",
                            "nomBeneficiaire": "ASS20",
                            "prenomBeneficiaire": "CHRISTIAN",
                            "dateNaissanceBeneficiaire": "1960-10-08",
                    }],
                    "page": {
                        "totalElements": 1
                    }
                }')
            ->willReturn([
                'content' => [
                    [
                            "numAdherent" => "07000020",
                            "nomBeneficiaire" => "ASS20",
                            "prenomBeneficiaire" => "CHRISTIAN",
                            "dateNaissanceBeneficiaire" => "1960-10-08"
                    ]
                ],
                'page' => [
                    'totalElements' => 1
                ]
            ]);

        $this->assertEquals([
            'columns' => [
                'labels' => [
                    'membershipNumber' => 'Numéro adhérent',
                    'beneficiaryName' => 'Nom'
                ],
                'config' => [
                    'membershipNumber' => [
                        'sortable' => true
                    ],
                    'beneficiaryName' => [
                        'sortable' => false
                    ]
                ]
            ],
            'advancedSearch' => [
                [
                    'fields' => [
                        [
                            'emptyOption' => 'Sélection en cours',
                            'name' => 'typology',
                            'type' => 'select',
                            'options' => ["Contrat Santé", "Contrat Prévoyance"]
                        ]
                    ],
                    'label' => 'Par typologie'
                ]
            ],
            'eligibleObjects' => [[
                'key' => 0,
                'membershipNumber' => '07000020',
                'openContractIdentifier' => null,
                'contractType' => null,
                'subcontractType' => null,
                'optionTop' => null,
                'cancellingContractDate' => null,
                'cancellingContractReason' => null,
                'conservationTime' => null,
                'settingsDescription' => null,
                'details' => [
                    'beneficiaryName' => 'ASS20',
                    'beneficiaryFirstname' => 'CHRISTIAN',
                    'beneficiaryBirthdate' => '08/10/1960',
                    'socialSecurityNumber' => null
                ]
            ]],
            'total' => 1
        ],
            $this->instance->find($requestMock)
        );
    }

    public function testBuildIndividualResults() : void
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $requestMock->expects($this->exactly(12))
            ->method('get')
            ->willReturnCallback(function ($parameter) {
                static $invocationCount = 0;
                $consecutiveArgs = [
                    'theme', 'environment', 'page', 'sortOrder',
                    'sortField', 'sortOrder', 'dateFrom', 'dateFrom',
                    'dateTo', 'dateTo', 'membershipNumber', 'membershipNumber',
                ];
                $consecutiveResults = [
                    'INDIVIDUS_ELIGIBLE', 'MERCERWA', 1, '1',
                    'name', '1', '2025-04-01', '2025-04-01',
                    '2025-04-30', '2025-04-30', '12345', '12345'
                ];

                $this->assertEquals($consecutiveArgs[$invocationCount], $parameter);

                return $consecutiveResults[$invocationCount++];
            });

        $this->uiConfigProviderMock->expects($this->once())
            ->method('convertFieldName')
            ->with(ObjectType::ELIGIBLE, 'INDIVIDUS_ELIGIBLE', 'name')
            ->willReturn('nom');

        $this->uiConfigProviderMock->expects($this->once())
            ->method('isThemeValid')
            ->with('INDIVIDUS_ELIGIBLE')
            ->willReturn(true);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getPropertyLabels')
            ->with(ObjectType::ELIGIBLE, 'INDIVIDUS_ELIGIBLE')
            ->willReturn([
                'membershipNumber' => 'Numéro adhérent',
                'familyLink' => 'Lien familial'
            ]);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getColumnsConfig')
            ->with(ObjectType::ELIGIBLE, 'INDIVIDUS_ELIGIBLE')
            ->willReturn([
                'membershipNumber' => [
                    'sortable' => true
                ],
                'familyLink' => [
                    'sortable' => false
                ]
            ]);

        $this->uiConfigProviderMock->expects($this->once())
            ->method('getAdvancedSearchConfig')
            ->with(ObjectType::ELIGIBLE, 'INDIVIDUS_ELIGIBLE')
            ->willReturn([
                [
                    'fields' => [
                        [
                            'name' => 'membershipNumber',
                            'type' => 'text'
                        ]
                    ],
                    'label' => 'Par numéro d\'adhérent'
                ]
            ]);

        $this->clientMock->expects($this->once())
            ->method('doRequest')
            ->with('/url/api-rgpd/v1/eligibles', [
                'environnement' => 'MERCERWA',
                'theme' => 'INDIVIDUS_ELIGIBLE',
                'pageable' => [
                    'sort' => [
                        [
                            'direction' => 'ASC',
                            'property' => 'nom'
                        ]
                    ],
                    'page' => 1,
                    'size' => 10
                ],
                'debutPeriode' => '2025-04-01',
                'finPeriode' => '2025-04-30',
                'numAdherent' => '12345'
            ], Request::METHOD_POST)
            ->willReturn([
                'content' => '{"content": [
                    {
                            "numAdherent": "07000020",
                            "nomBeneficiaire": "ASS20",
                            "prenomBeneficiaire": "CHRISTIAN",
                            "dateNaissanceBeneficiaire": "1960-10-08",
                    }],
                    "page": {
                        "totalElements": 1
                    }
                }',
                'headers' => []
            ]);

        $this->jsonDecoderMock->expects($this->once())
            ->method('decode')
            ->with('{"content": [
                    {
                            "numAdherent": "07000020",
                            "nomBeneficiaire": "ASS20",
                            "prenomBeneficiaire": "CHRISTIAN",
                            "dateNaissanceBeneficiaire": "1960-10-08",
                    }],
                    "page": {
                        "totalElements": 1
                    }
                }')
            ->willReturn([
                'content' => [
                    [
                        "numAdherent" => "07000020",
                        "nomBeneficiaire" => "ASS20",
                        "prenomBeneficiaire" => "CHRISTIAN",
                        "dateNaissanceBeneficiaire" => "1960-10-08"
                    ]
                ],
                'page' => [
                    'totalElements' => 1
                ]
            ]);

        $this->assertEquals([
            'columns' => [
                'labels' => [
                    'membershipNumber' => 'Numéro adhérent',
                    'familyLink' => 'Lien familial'
                ],
                'config' => [
                    'membershipNumber' => [
                        'sortable' => true
                    ],
                    'familyLink' => [
                        'sortable' => false
                    ]
                ]
            ],
            'advancedSearch' => [
                [
                    'fields' => [
                        [
                            'name' => 'membershipNumber',
                            'type' => 'text'
                        ]
                    ],
                    'label' => 'Par numéro d\'adhérent'
                ]
            ],
            'eligibleObjects' => [[
                'key' => 0,
                'familyLink' => null,
                'membershipNumber' => '07000020',
                'conservationTime' => null,
                'settingsDescription' => null,
                'details' => [
                    'beneficiaryName' => 'ASS20',
                    'beneficiaryFirstname' => 'CHRISTIAN',
                    'beneficiaryBirthdate' => '08/10/1960',
                    'socialSecurityNumber' => null
                ]
            ]],
            'total' => 1
        ],
            $this->instance->find($requestMock)
        );
    }

    public function testFindWithNoResult(): void
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $requestMock->expects($this->exactly(12))
            ->method('get')
            ->willReturnCallback(function ($parameter) {
                static $invocationCount = 0;
                $consecutiveArgs = [
                    'theme', 'environment', 'page', 'sortOrder',
                    'sortField', 'sortOrder', 'dateFrom', 'dateFrom',
                    'dateTo', 'dateTo', 'membershipNumber', 'membershipNumber',
                ];
                $consecutiveResults = [
                    'COTISATIONS_ELIGIBLE', 'MERCERWA', 1, '1',
                    'name', '1', '2025-04-01', '2025-04-01',
                    '2025-04-30', '2025-04-30', '12345', '12345'
                ];

                $this->assertEquals($consecutiveArgs[$invocationCount], $parameter);

                return $consecutiveResults[$invocationCount++];
            });

        $this->uiConfigProviderMock->expects($this->once())
            ->method('convertFieldName')
            ->with(ObjectType::ELIGIBLE, 'COTISATIONS_ELIGIBLE', 'name')
            ->willReturn('nom');

        $this->uiConfigProviderMock->expects($this->once())
            ->method('isThemeValid')
            ->with('COTISATIONS_ELIGIBLE')
            ->willReturn(true);

        $this->clientMock->expects($this->once())
            ->method('doRequest')
            ->with('/url/api-rgpd/v1/eligibles', [
                'environnement' => 'MERCERWA',
                'theme' => 'COTISATIONS_ELIGIBLE',
                'pageable' => [
                    'sort' => [
                        [
                            'direction' => 'ASC',
                            'property' => 'nom'
                        ]
                    ],
                    'page' => 1,
                    'size' => 10
                ],
                'debutPeriode' => '2025-04-01',
                'finPeriode' => '2025-04-30',
                'numAdherent' => '12345'
            ], Request::METHOD_POST)
            ->willReturn([
                'content' => '',
                'headers' => []
            ]);

        $this->assertEquals(['eligibleObjects' => []], $this->instance->find($requestMock));
    }

    /**
     * @return void
     */
    public function testFindWithUnknownTheme(): void
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $requestMock->expects($this->exactly(12))
            ->method('get')
            ->willReturnCallback(function ($parameter) {
                static $invocationCount = 0;
                $consecutiveArgs = [
                    'theme', 'environment', 'page', 'sortOrder',
                    'sortField', 'sortOrder', 'dateFrom', 'dateFrom',
                    'dateTo', 'dateTo', 'membershipNumber', 'membershipNumber',
                ];
                $consecutiveResults = [
                    'THEME_INCONNU', 'MERCERWA', 1, '1',
                    'name', '1', '2025-04-01', '2025-04-01',
                    '2025-04-30', '2025-04-30', '12345', '12345'
                ];

                $this->assertEquals($consecutiveArgs[$invocationCount], $parameter);

                return $consecutiveResults[$invocationCount++];
            });

        $this->uiConfigProviderMock->expects($this->once())
            ->method('convertFieldName')
            ->with(ObjectType::ELIGIBLE, 'THEME_INCONNU', 'name')
            ->willReturn('THEME_INCONNU');

        $this->uiConfigProviderMock->expects($this->once())
            ->method('isThemeValid')
            ->with('THEME_INCONNU')
            ->willReturn(false);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Unsupported theme: THEME_INCONNU');

        $this->assertEquals(['eligibleObjects' => []], $this->instance->find($requestMock));
    }

    /**
     * @return void
     * @throws ClientExceptionInterface
     * @throws DateMalformedStringException
     * @throws OidcException
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testFindToExport() : void
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $requestMock->expects($this->exactly(11))
            ->method('get')
            ->willReturnCallback(function ($parameter) {
                static $invocationCount = 0;
                $consecutiveArgs = [
                    'theme', 'environment', 'sortOrder',
                    'sortField', 'sortOrder', 'dateFrom', 'dateFrom',
                    'dateTo', 'dateTo', 'membershipNumber', 'membershipNumber',
                ];
                $consecutiveResults = [
                    'COTISATIONS_ELIGIBLE', 'MERCERWA', '1',
                    'name', '1', '2025-04-01', '2025-04-01',
                    '2025-04-30', '2025-04-30', '12345', '12345'
                ];

                $this->assertEquals($consecutiveArgs[$invocationCount], $parameter);

                return $consecutiveResults[$invocationCount++];
            });

        $this->uiConfigProviderMock->expects($this->once())
            ->method('convertFieldName')
            ->with(ObjectType::ELIGIBLE, 'COTISATIONS_ELIGIBLE', 'name')
            ->willReturn('nom');

        $this->clientMock->expects($this->once())
            ->method('doRequest')
            ->with('/url/api-rgpd/v1/exporter/eligibles', [
                'environnement' => 'MERCERWA',
                'theme' => 'COTISATIONS_ELIGIBLE',
                'pageable' => [
                    'sort' => [
                        [
                            'direction' => 'ASC',
                            'property' => 'nom'
                        ]
                    ]
                ],
                'debutPeriode' => '2025-04-01',
                'finPeriode' => '2025-04-30',
                'numAdherent' => '12345'
            ], Request::METHOD_POST)
            ->willReturn([
                'content' => 'content',
                'headers' => []
            ]);

        $this->assertEquals([
            'content' => 'content',
            'headers' => []
        ], $this->instance->findToExport($requestMock));
    }

    /**
     * @return void
     */
    public function testMakeExport(): void
    {
        $this->assertEquals([
            '/tmp/eligible_objects_' . date('Y-m-d') . '.csv',
            'eligible_objects_' . date('Y-m-d') . '.csv'
        ], $this->instance->makeExport('test', [
            'content-type' => [
                'text/csv'
            ],
        ]));

        $this->assertFileExists('/tmp/eligible_objects_' . date('Y-m-d') . '.csv');;
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        @unlink('/tmp/eligible_objects_' . date('Y-m-d') . '.csv');
    }
}