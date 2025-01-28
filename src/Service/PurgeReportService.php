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
readonly class PurgeReportService
{
    public function __construct(private Client $client, private readonly string $baseUrl)
    {
    }

    public function findPurgeReport(array $criteria): array
    {
        $responseContent = $this->client->doRequest($this->baseUrl . '/api-rgpd/v1/compte-rendu', $criteria);

        return json_decode($responseContent, true)['xxx'];
    }
}