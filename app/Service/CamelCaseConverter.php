<?php
namespace AdasFinance\Service;
use stdClass;

class CamelCaseConverter
{
    public static function convertToCamelCase($object) {
        if (is_object($object)) {
            $convertedObject = new stdClass();

            foreach ($object as $key => $value) {
                $convertedKey = self::convertKeyToCamelCase($key);
                $convertedObject->{$convertedKey} = $value;
            }

            return $convertedObject;
        }

        return $object;
    }

    private static function convertKeyToCamelCase($key) {
        $parts = preg_split('/[-_]/', $key);
        $parts = array_map('ucfirst', $parts);

        return lcfirst(implode('', $parts));
    }
}