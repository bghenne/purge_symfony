<?php

namespace App\Provider;

use App\Enum\ObjectType;
use LogicException;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

final class UiConfigProvider
{
    private array $config = [];

    public function __construct(private readonly string $projectDir, private readonly DecoderInterface $jsonDecoder)
    {
    }

    /**
     * Load config from json file
     *
     * @param ObjectType $objectType
     * @return void
     */
    private function loadConfig(ObjectType $objectType): void
    {
        if (!empty($this->config[$objectType->value])) {
            return; // we have been there already
        }

        $configJsonFile = $this->projectDir . '/referential/' . strtolower($objectType->value) . '/config.json';

        // fetch themes from json file
        if (!is_readable($configJsonFile)) {
            throw new LogicException('The config.json file is not readable.');
        }

        $configJsonFileContent = $this->jsonDecoder->decode(file_get_contents($configJsonFile), JsonEncoder::FORMAT);

        $config = $configJsonFileContent[$objectType->value];

        if (empty($config)) {
            throw new LogicException("Can not load config");
        }

        $this->config[$objectType->value] = $config;
    }

    /**
     * Extract config for a specific theme inside object type
     *
     * @param ObjectType $objectType
     * @param string $theme
     * @return array
     */
    private function extractThemeConfig(ObjectType $objectType, string $theme): array
    {
        // this methods assumes config is loaded
        foreach ($this->config[$objectType->value] as $themeConfig) {
            if (!empty($themeConfig[$theme])) {
                return $themeConfig[$theme];
            }
        }

        throw new LogicException("Can not load theme config");
    }


    /**
     * Property labels for Datatable columns translation
     *
     * @param ObjectType $objectType
     * @param string $theme
     * @return array
     */
    public function getPropertyLabels(ObjectType $objectType, string $theme): array
    {
        $this->loadConfig($objectType);
        $themeConfig = $this->extractThemeConfig($objectType, $theme);

        $labels = $themeConfig['columns']['labels'];
        $labelsList = [];

        foreach ($labels as $label) {
            $labelsList[$label['name']] = $label['translation'];
        }

        return $labelsList;
    }

    /**
     * Columns config needed for PrimeVue Datatable
     *
     * @param ObjectType $objectType
     * @param string $theme
     * @return array
     */
    public function getColumnsConfig(ObjectType $objectType, string $theme): array
    {
        $this->loadConfig($objectType);

        return $this->extractThemeConfig($objectType, $theme)['columns']['config'];
    }


    /**
     * Convert field name before giving it to webservice
     *
     * @param ObjectType $objectType
     * @param string $theme
     * @param string $fieldName
     * @return string
     */
    public function convertFieldName(ObjectType $objectType, string $theme, string $fieldName): string
    {
        $this->loadConfig($objectType);
        $themeConfig = $this->extractThemeConfig($objectType, $theme);

        $fields = $themeConfig['fields'];

        if (array_key_exists($fieldName, $fields)) {
            return $fields[$fieldName];
        }

        return $fieldName;
    }
}
