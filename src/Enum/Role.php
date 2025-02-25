<?php

namespace App\Enum;

/**
 * Class Role
 *
 * @package App\Enum
 * @category
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license
 * @copyright GFP Tech 2025
 */
enum Role : string
{
    case USER = 'ROLE_USER';
    case EXCLUSION = 'ROLE_EXCLUSION';

    case ADMIN = 'ROLE_ADMIN';
}