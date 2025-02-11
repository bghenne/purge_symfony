<?php

namespace App\Service;

use App\Http\Client;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EligibleObjectService
 *
 * @package App\Service
 * @category
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license
 * @copyright GFP Tech 2025
 */
readonly class EligibleObjectService
{
    public function __construct(private Client $client, private readonly string $baseUrl)
    {
    }

    /**
     * Find eligible objects based on criteria
     *
     * @return array
     */
    public function findEligibleObjects(array $criteria): array
    {
        $responseContent = $this->client->doRequest($this->baseUrl . '/api-rgpd/v1/eligibles', $criteria, Request::METHOD_POST);

        return json_decode($responseContent, true)['content'];
    }
}