<?php

namespace App\Enum;

/**
 * Each theme exists in a single page.
 */
enum Theme
{
    // Eligible objects
    case COTISATIONS_ELIGIBLE;
    case PRESTATIONS_SANTE_ELIGIBLE;
    case PRESTATIONS_PREVOYANCE_ELIGIBLE;
    case CONTRATS_OPTIONS_LIEN_SALARIAL_ELIGIBLE;
    case INDIVIDUS_ELIGIBLE;

    // Purged objects
    case COTISATIONS_PURGEE;
    case PRESTATIONS_SANTE_PURGEE;
    case PRESTATIONS_PREVOYANCE_PURGEE;
    case CONTRATS_OPTIONS_LIEN_SALARIAL_PURGEE;
    case INDIVIDUS_PURGEE;

    // Control alerts
    case COTISATIONS_ALERTE;
    case PRESTATIONS_SANTE_ALERTE;
    case PRESTATIONS_PREVOYANCE_ALERTE;
    case CONTRATS_OPTIONS_LIEN_SALARIAL_ALERTE;
    case INDIVIDUS_ALERTE;

    public static function values(): array
    {
        $values = [];

        foreach (self::cases() as $case) {
            $values[] = $case->name;
        }

        return $values;
    }
}
