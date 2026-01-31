<?php

namespace App\Traits;

/**
 * Model attribute birleştirme işlemleri için ortak metodlar
 */
trait MergesAttributes
{
    /**
     * Yeni değerler ile mevcut model değerlerini birleştir
     * Yeni değer varsa onu kullan, yoksa mevcut değeri koru
     *
     * @param array $newValues Yeni gelen değerler (farklı key formatında olabilir)
     * @param object $model Mevcut model instance
     * @param array $fieldMap [YeniKey => model_attribute] formatında mapping
     * @return array Birleştirilmiş değerler
     */
    protected function mergeWithExisting(array $newValues, object $model, array $fieldMap): array
    {
        $result = [];

        foreach ($fieldMap as $newKey => $modelAttribute) {
            $result[$modelAttribute] = array_key_exists($newKey, $newValues)
                ? $newValues[$newKey]
                : $model->{$modelAttribute};
        }

        return $result;
    }

    /**
     * Varsayılan değerlerle yeni kayıt için veri hazırla
     *
     * @param array $values Gelen değerler
     * @param array $fieldMap [GelenKey => [model_attribute, default_value]] formatında
     * @return array Hazırlanmış değerler
     */
    protected function prepareWithDefaults(array $values, array $fieldMap): array
    {
        $result = [];

        foreach ($fieldMap as $sourceKey => $config) {
            if (is_array($config)) {
                [$targetKey, $default] = $config;
            } else {
                $targetKey = $config;
                $default = null;
            }

            $result[$targetKey] = $values[$sourceKey] ?? $default;
        }

        return $result;
    }
}
