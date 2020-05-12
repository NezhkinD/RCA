<?php

namespace App\Http\Controllers\transformers;

use App\Http\Controllers\Controller;

class PolicyTransformer extends Controller implements Transformer
{
    private const REQUEST_HEADERS = [
        'Origin: https://dkbm-web.autoins.ru',
        'Referer: https://dkbm-web.autoins.ru/dkbm-web-1.0/policy.htm',
        'Connection: keep-alive',
        'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
    ];

    private const REQUEST_BODY = [
        'vin' => 'XTC651154E1303796',
        'lp' => '',
        'date' => '09.05.2020',
        'bodyNumber' => '',
        'chassisNumber' => '',
        'captcha' => '',
    ];

    public function getBody(string $captchaToken, array $options): array
    {
        $body = self::REQUEST_BODY;

        $body['vin'] = $options['vin'];

        $body['captcha'] = $captchaToken;

        $body['date'] = date('d.m.Y');

        return $body;
    }

    public function getHeaders(): array
    {
        return self::REQUEST_HEADERS;
    }
}
