<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface ServiceInterface
 *
 * @package App\Service
 * @category
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license
 * @copyright GFP Tech 2025
 */
interface ServiceInterface
{
    /**
     * Find data by calling webservice
     *
     * @param Request $request
     * @return array
     */
    public function find(Request $request) : array;

    /**
     * Extract parameters from the request
     *
     * @param Request $request
     * @param bool $withPagination
     * @return array
     */
    public function extractParameters(Request $request, bool $withPagination): array;

    /**
     * Find data to export
     *
     * @param Request $request
     * @return array
     */
    public function findToExport(Request $request): array;

    /**
     * Make csv or zip export
     *
     * @param string $content
     * @param array $headers
     * @return array
     */
    public function makeExport(string $content, array $headers): array;
}