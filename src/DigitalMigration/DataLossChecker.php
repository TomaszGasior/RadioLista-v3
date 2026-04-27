<?php

namespace App\DigitalMigration;

use App\Entity\RadioStation;
use ReflectionClass;
use ReflectionMethod;
use UnitEnum;

class DataLossChecker
{
    private const array IGNORED_RADIO_STATION_GETTERS = [
        'getId',
        'getRadioTable',
        'getMultiplex',
        'getDabChannel',
    ];

    /**
     * @return array Property names with data loss detected. No data loss if an empty array.
     */
    public function checkDataLoss(RadioStation $radioStation, ConvertionDecision $convertionDecision): array
    {
        if ($convertionDecision === ConvertionDecision::CONVERT_RADIO_STATION_TO_MULTIPLEX) {
            $propertiesToMigrate = ConvertionPropertiesMapping::COPY_RADIO_STATION_TO_MULTIPLEX_DIRECTLY;

            /**
             * RadioStation::$type will NOT be copied to Multiplex::$type because the latter doesn't exist.
             * However, RadioStation::$type was not nullable so it is required to accept this data loss
             * to avoid false positives here.
             */
            $propertiesToMigrate[] = 'Type';

            return $this->findPropertiesWithDataLoss($radioStation, $propertiesToMigrate);
        }

        if ($convertionDecision === ConvertionDecision::CONVERT_RADIO_STATION_TO_DIGITAL_RADIO_STATION) {
            $propertiesToMigrate = array_merge(
                ConvertionPropertiesMapping::COPY_RADIO_STATION_TO_MULTIPLEX_THROUGH_DIGITAL_RADIO_STATION_DEPENDENCY,
                ConvertionPropertiesMapping::COPY_RADIO_STATION_TO_DIGITAL_RADIO_STATION,
            );

            return $this->findPropertiesWithDataLoss($radioStation, $propertiesToMigrate);
        }

        return [];
    }

    private function findPropertiesWithDataLoss(RadioStation $radioStation, array $propertiesToMigrate): array
    {
        $getters = array_diff($this->getGetterNames($radioStation), self::IGNORED_RADIO_STATION_GETTERS);

        $propertiesWithDataLoss = [];

        foreach ($getters as $getterName) {
            $value = $radioStation->$getterName();
            $propertyName = substr($getterName, 3);

            $isValueEmpty = $this->isEmpty($value);
            $willValueBeMigrated = in_array($propertyName, $propertiesToMigrate);

            if (!$isValueEmpty && !$willValueBeMigrated) {
                $propertiesWithDataLoss[] = $propertyName;
            }
        }

        return $propertiesWithDataLoss;
    }

    private function getGetterNames(object $object): array
    {
        $methodNames = array_map(
            fn (ReflectionMethod $method) => $method->getName(),
            (new ReflectionClass($object))->getMethods()
        );

        $getterNames = array_filter(
            $methodNames,
            fn (string $methodName) => str_starts_with($methodName, 'get')
        );

        return $getterNames;
    }

    private function isObjectEmpty(object $object): bool
    {
        $getters = $this->getGetterNames($object);

        foreach ($getters as $getterName) {
            $value = $object->$getterName();

            if (!$this->isEmpty($value)) {
                return false;
            }
        }

        return true;
    }

    private function isEmpty(mixed $value): bool
    {
        if (is_object($value) && !($value instanceof UnitEnum)) {
            return $this->isObjectEmpty($value);
        }
        else {
            return $value === '' || $value === null || $value === [];
        }
    }
}
