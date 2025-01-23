<?php

namespace App\Tests\Service;

use App\Http\Client;
use App\Security\User;
use App\Service\EnvironmentService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

class EnvironmentServiceTest extends TestCase
{
    private MockObject $clientMock;

    private MockObject $securityMock;

    private MockObject $userMock;

    private EnvironmentService $instance;

    public function setUp(): void
    {
        $this->securityMock = $this->getMockBuilder(Security::class)
                                   ->disableOriginalConstructor()
                                   ->onlyMethods(['getUser'])
                                   ->getMock();

        $this->userMock = $this->getMockBuilder(User::class)
                               ->disableOriginalConstructor()
                               ->onlyMethods(['getEnvironments'])
                               ->getMock();

        $this->instance = new EnvironmentService($this->securityMock);

    }

    public function testGetEnvironmentsForList() : void
    {
        $this->securityMock->expects($this->once())
                           ->method('getUser')
                           ->willReturn($this->userMock);

        $this->userMock->expects($this->once())
                       ->method('getEnvironments')
                       ->willReturn([
                           17 => 'GFPPREV'
                       ]);

        $this->assertEquals([
            'GFPPREV' => 'GFPPREV'
        ], $this->instance->getEnvironmentsForList());

    }
}
