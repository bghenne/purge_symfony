<?php

namespace App\Trait;

use DateTime;
use Exception;

/**
 * Trait DateTrait
 *
 * @package Application\Traits
 * @category Trait
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license Open Web Purge
 * @copyright GFP France 2025
 */
trait DateTrait
{
    /**
     * Format date
     *
     * @param string $sDate
     * @param string $sInputFormat
     * @param string $sOutputFormat
     * @access protected
     *
     * @return string
     */
    public function formatDate(string $sDate, string $sInputFormat = 'Y-m-d H:i:s', string $sOutputFormat = 'd/m/Y H:i:s') : string
    {
        return DateTime::createFromFormat($sInputFormat, $sDate)->format($sOutputFormat);
    }

    /**
     * Get today
     *
     * @access public
     *
     * @return DateTime
     * @throws Exception
     */
    public function getToday() : DateTime
    {
        return new DateTime(date('Y-m-d'));
    }

    /**
     * @param DateTime $oDate
     * @param DateTime $oFirstDate
     * @param DateTime $oEndDate
     * @param bool $bIsEqual
     * @return bool
     */
    public function isBetween(DateTime $oDate, DateTime $oFirstDate, DateTime $oEndDate, bool $bIsEqual = false): bool
    {
        return (
            ($oDate > $oFirstDate && $oDate < $oEndDate)
            || ($bIsEqual && $oDate >= $oFirstDate && $oDate <= $oEndDate)
        );
    }
}