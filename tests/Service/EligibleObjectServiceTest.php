<?php

namespace App\Tests\Service;

use App\Enum\ObjectType;
use App\Http\Client;
use App\Provider\UiConfigProvider;
use App\Service\EligibleObjectService;
use DateMalformedStringException;
use Drenso\OidcBundle\Exception\OidcException;
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
            ->onlyMethods(['convertFieldName', 'getPropertyLabels', 'getColumnsConfig', 'getAdvancedSearchConfig'])
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
    public function testFind(): void
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
                    'labels' => 'Par date de paiement'
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
                        'labels' => 'Par date de paiement'
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

}