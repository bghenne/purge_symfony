<?php

namespace App\Trait;

use DateMalformedStringException;
use DateTime;
use DateTimeImmutable;
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
     * @param string $date
     * @param string $inputFormat
     * @param string $outputFormat
     * @access protected
     *
     * @return string
     */
    public function formatDate(string $date, string $inputFormat = 'Y-m-d', string $outputFormat = 'd/m/Y') : string
    {
        return DateTime::createFromFormat($inputFormat, $date)->format($outputFormat);
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
     * @param DateTime $date
     * @param DateTime $firstDate
     * @param DateTime $endDate
     * @param bool $isEqual
     * @return bool
     */
    public function isBetween(DateTime $date, DateTime $firstDate, DateTime $endDate, bool $isEqual = false): bool
    {
        return (
            ($date > $firstDate && $date < $endDate)
            || ($isEqual && $date >= $firstDate && $date <= $endDate)
        );
    }

    /**
     * Format date given by Vue Datepicker plugin
     *
     * @param string $datetime
     * @return string
     * @throws DateMalformedStringException
     * @example Tue Feb 18 2025 00:00:00 GMT+0100 will become 2025-02-18
     *
     */
    public function convertDateFromString(string $datetime) : string
    {
        return new DateTimeImmutable($datetime)->format('Y-m-d');
    }
}