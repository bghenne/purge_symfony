<?php

namespace App\Http;

use Drenso\OidcBundle\Exception\OidcException;
use Drenso\OidcBundle\OidcClientInterface;
use Drenso\OidcBundle\Security\Token\OidcToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class Client
 *
 * @package App\Http\Client
 * @category
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license
 * @copyright GFP Tech 2024
 */
class Client
{
    private string $accessToken = '';
    private string $refreshToken = '';

    public function __construct(
        private HttpClientInterface            $httpClient,
        private readonly string                $baseUrl,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly OidcClientInterface   $oidcClient
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface|OidcException
     */
    public function doRequest(string $url, ?array $parameters = [], string $method = Request::METHOD_GET): string
    {
        $requestParameters = (in_array($method, [Request::METHOD_DELETE, Request::METHOD_GET]) ? ['query' => $parameters] : ['body' => json_encode($parameters)]);

        $this->createClient();
        $response = $this->httpClient->request($method, $url, $requestParameters);

        if (Response::HTTP_UNAUTHORIZED === $response->getStatusCode()) {

            $oidcTokens = $this->oidcClient->refreshTokens($this->refreshToken);

            // update token in session
            // this is kind of workaround as it's not really possible to save the session again
            $this->tokenStorage->getToken()->setAttribute(OidcToken::AUTH_DATA_ATTR, $oidcTokens);

            // need to recreate the client to update access token passed
            $this->createClient($oidcTokens->getAccessToken());

            $response = $this->httpClient->request($method, $url, $requestParameters);
        }

        return $response->getContent();
    }

    protected function createClient(?string $accessToken = null): void
    {;
        $options = [
            'base_uri' => $this->baseUrl
        ];

        if (!empty($accessToken)) {
            $options['headers']['Authorization'] = 'Bearer ' . $accessToken;
        } elseif (($token = $this->tokenStorage->getToken()) instanceof OidcToken) {
            $oidcTokens = $token->getAuthData();
            $this->accessToken = $oidcTokens->getAccessToken();
            $this->refreshToken = $oidcTokens->getRefreshToken();
            $options['headers']['Authorization'] = 'Bearer ' . $this->accessToken;
        }

        $this->httpClient = $this->httpClient->withOptions($options);
    }

}