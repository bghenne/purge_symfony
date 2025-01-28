<?php

namespace App\Tests\Http;

use App\Http\Client;
use Drenso\OidcBundle\Model\OidcTokens;
use Drenso\OidcBundle\OidcClient;
use Drenso\OidcBundle\Security\Token\OidcToken;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ClientTest extends TestCase
{
    private MockObject $clientMock;

    private TokenStorage $tokenStorageMock;

    private OidcClient $oidcClientMock;

    private Client $instance;

    public function setUp(): void
    {
        $this->clientMock = $this->getMockBuilder(MockHttpClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['request', 'withOptions'])
            ->getMock();

        $this->tokenStorageMock = $this->getMockBuilder(TokenStorage::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getToken'])
            ->getMock();

        $this->oidcClientMock = $this->getMockBuilder(OidcClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['refreshTokens'])
            ->getMock();

        $this->instance = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([$this->clientMock, $this->tokenStorageMock, $this->oidcClientMock])
            ->onlyMethods(['createClient'])
            ->getMock();
    }

    public function testDoRequest(): void
    {
        $responseMock = $this->getMockBuilder(MockResponse::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStatusCode', 'getContent'])
            ->getMock();

        $this->instance->expects($this->once())
            ->method('createClient');

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with(Request::METHOD_GET, '/', ['query' => []])
            ->willReturn($responseMock);

        $responseMock->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $responseMock->expects($this->once())
            ->method('getContent')
            ->willReturn('{"foo":"bar"}');

        $this->assertEquals('{"foo":"bar"}', $this->instance->doRequest('/', [], Request::METHOD_GET));
    }

    public function testDoRequestWithTokenRenewal(): void
    {
        $responseMock = $this->getMockBuilder(MockResponse::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStatusCode', 'getContent'])
            ->getMock();

        $oidcTokensMock = $this->getMockBuilder(OidcTokens::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessToken'])
            ->getMock();

        $oidcTokenMock = $this->getMockBuilder(OidcToken::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setAttribute'])
            ->getMock();

        $this->instance->expects($this->exactly(2))
            ->method('createClient')
            ->willReturnCallback(function (?string $accessToken) {
                static $invocationCount = 0;
                $consecutiveArgs = [
                    [null],
                    ['accessToken'],
                ];

                $consecutiveResults = [null, null];
                $this->assertEquals($consecutiveArgs[$invocationCount][0], $accessToken);

                return $consecutiveResults[$invocationCount++];
            });


        $this->clientMock->expects($this->exactly(2))
            ->method('request')
            ->willReturnCallback(function (string $method, string $url, array $options = []): MockResponse {
                static $invocationCount = 0;
                $consecutiveArgs = [
                    [Request::METHOD_GET, '/', ['query' => []]],
                    [Request::METHOD_GET, '/', ['query' => []]]
                ];

                $consecutiveResults = [null, null];
                $this->assertEquals($consecutiveArgs[$invocationCount][0], $method);
                $this->assertEquals($consecutiveArgs[$invocationCount][1], $url);
                $this->assertEquals($consecutiveArgs[$invocationCount][2], $options);

                return $consecutiveResults[$invocationCount++];
            })
            ->willReturn($responseMock);

        $responseMock->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(401);

        $this->oidcClientMock->expects($this->once())
            ->method('refreshTokens')
            ->with('')
            ->willReturn($oidcTokensMock);

        $oidcTokensMock->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('accessToken');

        $this->tokenStorageMock->expects($this->once())
            ->method('getToken')
            ->willReturn($oidcTokenMock);

        $oidcTokenMock->expects($this->once())
            ->method('setAttribute')
            ->with(OidcToken::AUTH_DATA_ATTR, $oidcTokensMock);

        $responseMock->expects($this->once())
            ->method('getContent')
            ->willReturn('{"foo":"bar"}');

        $this->assertEquals('{"foo":"bar"}', $this->instance->doRequest('/', [], Request::METHOD_GET));
    }

    public function testCreateClient(): void
    {
        $oidcTokenMock = $this->getMockBuilder(OidcToken::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAuthData'])
            ->getMock();

        $oidcTokensMock = $this->getMockBuilder(OidcTokens::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAccessToken', 'getRefreshToken'])
            ->getMock();

        $this->tokenStorageMock->expects($this->once())
            ->method('getToken')
            ->willReturn($oidcTokenMock);

        $oidcTokenMock->expects($this->once())
            ->method('getAuthData')
            ->willReturn($oidcTokensMock);

        $oidcTokensMock->expects($this->once())
            ->method('getAccessToken')
            ->willReturn('accessToken');

        $oidcTokensMock->expects($this->once())
            ->method('getRefreshToken')
            ->willReturn('refreshToken');

        $this->clientMock->expects($this->once())
            ->method('withOptions')
            ->with([
                'headers' => [
                    'Authorization' => 'Bearer accessToken',
                ]
            ]);

        $reflectionMethod = new ReflectionMethod(Client::class, 'createClient');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($this->instance);

    }

    public function testCreateClientWithAccessToken(): void
    {
        $this->clientMock->expects($this->once())
            ->method('withOptions')
            ->with([
                'headers' => [
                    'Authorization' => 'Bearer accessToken',
                ]
            ]);

        $reflectionMethod = new ReflectionMethod(Client::class, 'createClient');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($this->instance, 'accessToken');

    }
}
