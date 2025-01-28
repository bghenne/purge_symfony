<?php

namespace App\Service;

use App\Http\Client;

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
    public function __construct(private Client $client)
    {
    }

    /**
     * Find eligible objects based on criteria
     *
     * @return array
     */
    public function findEligibleObjects(array $criteria): array
    {
        $responseContent = $this->client->doRequest('eligibles', $criteria);

        return json_decode($responseContent, true)['xxx'];
    }
}