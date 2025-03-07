<?php

namespace App\Service;

use App\Enum\Theme;

final class UiConfigProvider
{
    private $propertylabels = [
        'beneficiaryBirthdate' => "Date de naissance du bénéficiaire",
        'beneficiaryFirstname' => "Prénom",
        'beneficiaryName' => "Nom",
        'campaignDate' => 'Date de la campagne',
        'clientName' => 'Nom du client',
        'conservationTime' => 'Délai de conservation appliqué',
        'contributionCallPeriod' => 'Période des cotisations en mois',
        'contributionCallYear' => 'Période des cotisations en année',
        'contributionPaymentDate' => 'Date de dernier paiement de la cotisation',
        'familyId' => 'Identifiant de famille',
        'socialSecurityNumber' => 'Numéro de Sécurité sociale',
    ];

    /**
     * @param List<string> $properties
     *
     * @return string[]
     */
    public function getPropertyLabels(array $properties): array
    {
        $mapping = [];

        foreach ($properties as $property) {
            if (false === isset($this->propertylabels[$property])) {
                throw new \LogicException(sprintf(
                    'Unknown property (%s)',
                    $property
                ));
            }
            $mapping[$property] = $this->propertylabels[$property];
        }

        return $mapping;
    }

    public function getColumnsConfig(Theme $themeCode): array
    {

    }
}
