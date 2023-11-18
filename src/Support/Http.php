<?php

namespace Pkg6\DBALW\Support;


class Http
{

    /**
     * @param $url
     * @return bool|string
     */
    public static function get($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //参数为1表示传输数据，为0表示直接输出显示。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //参数为0表示不带头文件，为1表示带头文件
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // 关闭SSL验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $response = curl_exec($ch);
        if ($err = curl_error($ch)) {
            throw new \RuntimeException("GET {$url} {$err}");
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode != 200) {
            throw new \RuntimeException("GET {$url} HTTP_CODE {$httpCode}");
        }
        return $response;
    }
}