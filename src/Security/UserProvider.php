<?php

namespace App\Security;

use App\Http\Client;
use Drenso\OidcBundle\Model\OidcUserData;
use Drenso\OidcBundle\Security\Exception\OidcUserNotFoundException;
use Drenso\OidcBundle\Security\UserProvider\OidcUserProviderInterface;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 */
readonly class UserProvider implements OidcUserProviderInterface
{
    public function __construct(
        private Client $client,
        private DecoderInterface $jsonDecode,
        private string $applicationName,
        private string $baseUrl
    ) {}

    /**
     * Check that user exists in database
     */
    public function ensureUserExists(string $userIdentifier, OidcUserData $userData): mixed
    {
        return $this->loadUserByIdentifier($userIdentifier) instanceof User;
    }

    /**
     * Same as loadUserByIdentifier but oidc specific
     */
    public function loadOidcUser(string $userIdentifier): UserInterface
    {
        return $this->loadUserByIdentifier($userIdentifier);
    }

    /**
     * Refresh user based on identifier
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    /**
     * All classes extending User are supported
     */
    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }

    /**
     * Load user by calling doctrine repository
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $roles = $environments = $dbEnvironments = $visibilityUnits = [];

        try {

            $responseContent = $this->client->doRequest($this->baseUrl . '/api-authorization-rest/1.0/users', ['user' => $identifier]);

            $userData = $this->jsonDecode->decode($responseContent, JsonEncoder::FORMAT)['users'][0];

            $responseContent = $this->client->doRequest($this->baseUrl . '/api-authorization-rest/1.0/users/roles', ['application' => $this->applicationName, 'user' => $identifier]);

            $userRoles = $this->jsonDecode->decode($responseContent, JsonEncoder::FORMAT)['userRoles'];

            foreach ($userRoles as $userRole) {
                // all roles must be prefixed by ROLE_ in Symfony
                $roles[] = 'ROLE_' . $userRole['role'];
            }

            $responseContent = $this->client->doRequest($this->baseUrl . '/api-authorization-rest/1.0/users/environments', ['application' => $this->applicationName, 'user' => $identifier]);

            $userEnvironments = $this->jsonDecode->decode($responseContent, JsonEncoder::FORMAT)['userEnvironments'];

            foreach ($userEnvironments as $userEnvironment) {
                $environments[] = $userEnvironment['environment'];
            }

            $responseContent = $this->client->doRequest($this->baseUrl . '/api-referential-rest/2.0/environments');

            $databaseEnvironments = $this->jsonDecode->decode($responseContent, JsonEncoder::FORMAT)['environments'];

            foreach ($databaseEnvironments as $databaseEnvironment) {
                $dbEnvironments[] = $databaseEnvironment['name'];
            }

            $responseContent = $this->client->doRequest($this->baseUrl . '/api-authorization-rest/1.0/users-visibilities-units', ['selected' => 'true', 'user' => $identifier]);

            $userVisibilityUnits = $this->jsonDecode->decode($responseContent, JsonEncoder::FORMAT)['visibilitiesUnits'];

            foreach ($userVisibilityUnits as $userVisibilityUnit) {
                $visibilityUnits[] = $userVisibilityUnit['code'];
            }

        } catch (Exception $e) {
            throw new OidcUserNotFoundException($e->getMessage());
        }

        if (empty($userData)) {
            throw new OidcUserNotFoundException('User not found');
        }

        $user = new User();
        $user->setFirstName($userData['firstName'])
            ->setLastName($userData['surName'])
            ->setUsername($identifier)
            ->setRoles($roles)
            ->setEnvironments(array_values(array_intersect($environments, $dbEnvironments))) // display only environments available for user and on the server
            ->setVisibilityUnits($visibilityUnits);

        return $user;
    }
}