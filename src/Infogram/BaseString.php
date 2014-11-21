<?php

namespace Infogram;

class BaseString
{
    public static function compute($method, $url, $params)
    {
        $parts = array();

        $parts []= strtoupper($method);

        $parts []= self::percentEncode($url);

        $keys = array_keys($params);
        sort($keys, SORT_STRING);

        $upperBound = count($keys) - 1;
        $paramPart = '';
        for ($i = 0; $i <= $upperBound; $i++) {
            $key = $keys[$i];
            $value = $params[$key];
            $pair = self::percentEncode($key) . '=' . self::percentEncode($value);
            if ($i < $upperBound) {
                $pair .= '&';
            }
            $paramPart .= $pair;
        }
        $parts []= self::percentEncode($paramPart);

        return implode('&', $parts);
    }

    private static function percentEncode($in) {
        return rawurlencode($in);
    }
}
