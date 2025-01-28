<?php

namespace App\Service;

use App\Http\Client;

/**
 * Class PurgedObjectService
 *
 * @package App\Service
 * @category
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license
 * @copyright GFP Tech 2025
 */
readonly class PurgedObjectService
{
    public function __construct(private Client $client)
    {
    }

    public function findPurgedObjects(array $criteria): array
    {
        $responseContent = $this->client->doRequest('donnees-purgees', $criteria);

        return json_decode($responseContent, true)['xxx'];
    }
}