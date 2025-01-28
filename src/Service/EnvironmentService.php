<?php

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;

/**
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
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