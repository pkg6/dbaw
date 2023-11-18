<?php

namespace Pkg6\DBALW\Support;


use Pkg6\DBALW\Exceptions\FieldDataException;

class Arr
{

    /**
     * @param array $data
     * ["id" => 1, "name" => "aaa"]
     * [["id" => 1, "name" => "aaa"], ["id" => 2, "name" => "bbb"]]
     * @param array $fields
     * ["id"]
     * @return array
     * ["name" => "aaa"]
     * ["name" => "aaa"], ["name" => "bbb"]]
     */
    public static function ignoreField(&$data, $fields = [])
    {
        if (empty($fields)) {
            return $data;
        }
        switch (Arr::getArrayDimensions($data)) {
            case 1:
                foreach ($fields as $field) {
                    if (isset($data[$field])) {
                        unset($data[$field]);
                    }
                }
                break;
            case 2:
                foreach ($data as &$datum) {
                    foreach ($fields as $field) {
                        if (isset($datum[$field])) {
                            unset($datum[$field]);
                        }
                    }
                }
                break;
        }
        return $data;
    }

    /**
     * @param array $fields
     * ["id", "name"]
     * @param array $data
     * ["id" => 1, "name" => "aaa"]
     * [["id" => 1, "name" => "aaa"], ["id" => 2, "name" => "bbb"]]
     * @return array
     */
    public static function fieldsData(array $fields, array $data)
    {
        $newData = [];
        switch (Arr::getArrayDimensions($data)) {
            case 1:
                foreach ($fields as $field) {
                    if (isset($data[$field])) {
                        $newData[$field] = $data[$field];
                    } else {
                        throw new FieldDataException($field, $data);
                    }
                }
                break;
            case 2:
                foreach ($data as $i=> $datum) {
                    foreach ($fields as $field) {
                        if (isset($datum[$field])) {
                            $newData[$i][$field] = $datum[$field];
                        } else {
                            throw new FieldDataException($field, $datum);
                        }
                    }
                }
                break;
            default:
                throw new FieldDataException(reset($fields), $data);
        }
        return $newData;

    }

    /**
     * 获取数组的维度
     * @param $array
     * @return int|mixed
     */
    public static function getArrayDimensions($array)
    {
        if (!is_array($array) || empty($array)) {
            return 0;
        }
        $dimensions = 1;
        foreach ($array as $element) {
            if (is_array($element)) {
                $dimensions = max($dimensions, 1 + Arr::getArrayDimensions($element));
            }
        }
        return $dimensions;
    }
}