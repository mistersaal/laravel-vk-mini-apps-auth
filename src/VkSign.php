<?php


namespace Mistersaal\VkMiniAppsAuth;

use Mistersaal\VkMiniAppsAuth\Exceptions\VkSignException;


class VkSign
{
    /**
     * @param string $url
     * @return array
     * @throws VkSignException
     */
    public function getParams(string $url)
    {
        $client_secret = config('vkminiapps.app.secret'); //Защищённый ключ из настроек вашего приложения

        $query_params = [];
        parse_str(parse_url($url, PHP_URL_QUERY), $query_params); // Получаем query-параметры из URL

        $sign_params = [];
        foreach ($query_params as $name => $value) {
            if (strpos($name, 'vk_') !== 0) { // Получаем только vk параметры из query
                continue;
            }

            $sign_params[$name] = $value;
        }

        ksort($sign_params); // Сортируем массив по ключам
        $sign_params_query = http_build_query($sign_params); // Формируем строку вида "param_name1=value&param_name2=value"
        $sign = rtrim(strtr(base64_encode(hash_hmac('sha256', $sign_params_query, $client_secret, true)), '+/', '-_'), '='); // Получаем хеш-код от строки, используя защищеный ключ приложения. Генерация на основе метода HMAC.

        if (! isset($query_params['sign'])) {
            throw new VkSignException("No signature.");
        }
        $status = $sign === $query_params['sign']; // Сравниваем полученную подпись со значением параметра 'sign'

        if (! $status) {
            throw new VkSignException("Invalid sign.");
        }
        return $sign_params;
    }
}
