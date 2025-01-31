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
        $criteria = [
            'environnement' => 'MERCERW2',
            'theme' => 'COTISATIONS',
            'debutPeriode' => '2018-01-01',
            'finPeriode' => '2018-12-31',
            'idFass' => 15,
            'idPrestation' => 0,
            'typePrestation' => 'string'

        ];


        $responseContent = $this->client->doRequest($this->baseUrl . '/api-rgpd/v1/eligibles', $criteria, Request::METHOD_POST);



        return json_decode($responseContent, true)['cotisations'];
    }
}