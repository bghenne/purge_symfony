<?php

namespace App\Service;

use App\Enum\ObjectType;
use InvalidArgumentException;
use LogicException;

/**
 * Class ThemeService
 *
 * @package App\Service
 * @category Service
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license Open Web Purge
 * @copyright GFP Tech 2025
 */
readonly class ThemeService
{
    public function __construct(private readonly string $projectDir)
    {
    }


    /**
     * @param string $objectType
     * @return array
     */
    public function findThemesByObjectType(string $objectType): array
    {
        $objectTypes = ObjectType::values();

        if (!in_array($objectType, $objectTypes, true)) {
            throw new InvalidArgumentException("Object type $objectType is not a valid object type.");
        }

        $themesJsonFile = $this->projectDir . '/themes.json';

        // fetch themes from json file
        if (!is_readable($themesJsonFile)) {
            throw new LogicException('The themes.json file is not readable.');
        }

        return json_decode(file_get_contents($themesJsonFile), true);

    }
}