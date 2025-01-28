<?php

namespace App\Tests\Service;

use App\Http\Client;
use App\Service\EligibleObjectService;
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
class EligibleObjectServiceTest extends TestCase
{
    private MockObject $clientMock;

    private EligibleObjectService $instance;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->clientMock = $this->getMockBuilder(Client::class)
                                 ->disableOriginalConstructor()
                                 ->onlyMethods(['doRequest'])
                                 ->getMock();

        $this->instance = new EligibleObjectService($this->clientMock);
    }

    /**
     * @return void
     */
    public function testFindEligibleObjects(): void
    {
        $this->clientMock->expects($this->once())->method('doRequest')
                         ->with('eligibles', [])
                         ->willReturn('');

        $this->assertIsArray($this->instance->findEligibleObjects([]));

        // todo complete test
    }

}