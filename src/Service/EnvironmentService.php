<?php

namespace App\Service;

use App\Http\Client;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Class EnvironmentService
 *
 * @package App\Service
 * @category
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license
 * @copyright GFP Tech 2024
 */
readonly class EnvironmentService
{
    public function __construct(private readonly Security $security)
    {}

    public function getEnvironmentsForList() : ?array
    {
        $environments = $this->security->getUser()->getEnvironments();
        $environmentsList = [];

        foreach ($environments as $environmentName) {
            $environmentsList[$environmentName] = $environmentName;
        }

        return $environmentsList;
    }
}