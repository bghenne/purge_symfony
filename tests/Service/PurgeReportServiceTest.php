<?php

namespace App\Tests\Service;

use App\Http\Client;
use App\Service\PurgedObjectService;
use App\Service\PurgeReportService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class EligibleObjectServiceTest
 *
 * @package App\Tests\Service
 * @category
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license
 * @copyright GFP Tech 2025
 */
class PurgeReportServiceTest extends TestCase
{
    private MockObject $clientMock;

    private PurgeReportService $instance;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->clientMock = $this->getMockBuilder(Client::class)
                                 ->disableOriginalConstructor()
                                 ->onlyMethods(['doRequest'])
                                 ->getMock();

        $this->instance = new PurgeReportService($this->clientMock, '/url');
    }

    /**
     * @return void
     */
    public function testFindPurgeReport(): void
    {
        $this->clientMock->expects($this->once())->method('doRequest')
                         ->with('/url/api-rgpd/v1/compte-rendu', [])
                         ->willReturn('');

        $this->assertIsArray($this->instance->findPurgeReport([]));

        // todo complete test
    }

}