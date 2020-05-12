<?php


namespace App\Http\Controllers\transformers;

use App\Http\Controllers\Controller;

class BsostateTransformer extends Controller implements Transformer
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
        'bsoseries' => 'МММ',
        'bsonumber' => '5011759230',
        'captcha' => '',
    ];

    public function getBody(string $captchaToken, array $options): array
    {
        $body = self::REQUEST_BODY;

        $body['bsoseries'] = $options['bsoseries'];

        $body['bsonumber'] = $options['bsonumber'];

        $body['captcha'] = $captchaToken;

        return $body;
    }

    public function getHeaders(): array
    {
        return self::REQUEST_HEADERS;
    }
}
