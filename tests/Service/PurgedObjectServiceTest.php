<?php

namespace App\Tests\Service;

use App\Http\Client;
use App\Service\EligibleObjectService;
use App\Service\PurgedObjectService;
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
class PurgedObjectServiceTest extends TestCase
{
    private MockObject $clientMock;

    private PurgedObjectService $instance;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->clientMock = $this->getMockBuilder(Client::class)
                                 ->disableOriginalConstructor()
                                 ->onlyMethods(['doRequest'])
                                 ->getMock();

        $this->instance = new PurgedObjectService($this->clientMock);
    }

    /**
     * @return void
     */
    public function testFindPurgedObjects(): void
    {
        $this->clientMock->expects($this->once())->method('doRequest')
                         ->with('donnees-purgees', [])
                         ->willReturn('');

        $this->assertIsArray($this->instance->findPurgedObjects([]));

        // todo complete test
    }

}