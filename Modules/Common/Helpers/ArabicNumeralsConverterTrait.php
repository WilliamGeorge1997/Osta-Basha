<?php

namespace Modules\Common\Helpers;

trait ArabicNumeralsConverterTrait
{
    /**
     * Arabic and Western numerals mapping
     */
    private static $toWesternMap = null;
    private static $toArabicMap = null;

    /**
     * Fields to convert in requests (empty means convert all)
     */
    protected $fieldsToConvert = [];

    /**
     * Prepare the data for validation by converting Arabic numerals to Western numerals
     */
    protected function prepareForValidation()
    {
        $data = $this->all();
        $this->replace($this->convertToWestern($data, $this->fieldsToConvert));
    }

    /**
     * Convert data to Arabic numerals for resources
     */
    protected function convertNumericToArabic($data, $fields = [])
    {
        return $this->convertToArabic($data, $fields);
    }

    /**
     * Convert data from Arabic to Western numerals
     */
    private function convertToWestern($data, $fields = [])
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (empty($fields) || in_array($key, $fields) || is_array($value)) {
                    $data[$key] = $this->convertToWestern($value, $fields);
                }
            }
            return $data;
        }

        if (is_string($data)) {
            if (self::$toWesternMap === null) {
                self::$toWesternMap = [
                    '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
                    '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9'
                ];
            }

            if (preg_match('/[٠-٩]/', $data)) {
                return strtr($data, self::$toWesternMap);
            }
        }

        return $data;
    }

    /**
     * Convert data from Western to Arabic numerals
     */
    private function convertToArabic($data, $fields = [])
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (empty($fields) || in_array($key, $fields) || is_array($value)) {
                    $data[$key] = $this->convertToArabic($value, $fields);
                }
            }
            return $data;
        }

        if (is_string($data) || is_numeric($data)) {
            if (self::$toArabicMap === null) {
                self::$toArabicMap = [
                    '0' => '٠', '1' => '١', '2' => '٢', '3' => '٣', '4' => '٤',
                    '5' => '٥', '6' => '٦', '7' => '٧', '8' => '٨', '9' => '٩'
                ];
            }

            $stringData = (string)$data;
            if (preg_match('/[0-9]/', $stringData)) {
                return strtr($stringData, self::$toArabicMap);
            }
        }

        return $data;
    }
}