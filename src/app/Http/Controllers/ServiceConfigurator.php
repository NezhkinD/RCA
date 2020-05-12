<?php


namespace App\Http\Controllers;


class ServiceConfigurator extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Checks Creator. Добавление проверок
    |--------------------------------------------------------------------------
    |
    */

    public const CHECKS_CREATOR__REDIS_LIST = 'checks_list';

    public const CHECKS_CREATOR__ALGO_HASH_COLUMN = 'ripemd128';

    public const CHECKS_CREATOR__REQUEST_REGEX = '/vin|number|body|chassis/m';




    // -----------------------------------------------------------------------------------------------------------------
    // Check Handler. Выполнение проверки на получение сведений о полисе ОСАГО
    //------------------------------------------------------------------------------------------------------------------

    public const CHECK_HANDLER_REQUEST_URL = 'https://dkbm-web.autoins.ru/dkbm-web-1.0/policy.htm';

    public const CHECK_HANDLER__SITE_KEY = '6Lf2uycUAAAAALo3u8D10FqNuSpUvUXlfP7BzHOk';


    // -----------------------------------------------------------------------------------------------------------------
    // Test
    //------------------------------------------------------------------------------------------------------------------

    // auth key
    // RPA_1::b7nfer0e80-bm948cse3k-6rim0bv0r9-0dtpsibb0z

    public const INPUT_LIMIT_NUMBERS = 500;

    // -----------------------------------------------------------------------------------------------------------------
    // Recaptha
    //------------------------------------------------------------------------------------------------------------------
    public const RECAPTHA_SEND_URL = 'http://80.66.93.229:443/in';

    public const RECAPTHA_AUTH_KEY = 'e1de9a9c2545e2c5be2de2aaf36d3bf8';

    public const RECAPTHA_V2_METHOD = 'userrecaptcha';

    // -----------------------------------------------------------------------------------------------------------------
    // Auth
    //------------------------------------------------------------------------------------------------------------------

    public const AUTH_PEPPER = 'N#~^uY4r"+u,L>=cVm!4ARk@zVbzWM5\{4=raFx3hQyTDjN.n';

    public const AUTH_ALGO = PASSWORD_ARGON2ID;


    // -----------------------------------------------------------------------------------------------------------------
    // Request
    //------------------------------------------------------------------------------------------------------------------


    public const API_INPUT_JSON_SCHEME = '{ "number": "string", "type": "string" }';

    public const REQUEST_HEADERS = [
        'Origin: https://dkbm-web.autoins.ru',
        'Referer: https://dkbm-web.autoins.ru/dkbm-web-1.0/policy.htm',
        'Connection: keep-alive',
        'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'
    ];


    // -----------------------------------------------------------------------------------------------------------------
    // Policy. Get info
    //------------------------------------------------------------------------------------------------------------------

    public const POLICY_REQUEST_URL = 'https://dkbm-web.autoins.ru/dkbm-web-1.0/policy.htm';


    public const POLICY_REQUEST_BODY = [
        'vin' => 'XTC651154E1303796',
        'lp' => '',
        'date' => '19.02.2020',
        'bodyNumber' => '',
        'chassisNumber' => '',
        'captcha' => '',
    ];

    public const POLICY_REDIS_LIST = 'policy_work';

    //public const POLICY_RECAPTHA_SITE_KEY = '6Lf2uycUAAAAALo3u8D10FqNuSpUvUXlfP7BzHOk';

    // -----------------------------------------------------------------------------------------------------------------
    // Bsostate. Get info
    //------------------------------------------------------------------------------------------------------------------

    public const BSOSTATE_REQUEST_URL = 'https://dkbm-web.autoins.ru/dkbm-web-1.0/bsostate.htm';

    public const BSOSTATE_REQUEST_BODY = [
        'bsoseries' => 'МММ',
        'bsonumber' => '5011759230',
        'captcha' => '',
    ];


    /*
     *  МММ
     * 5011759230
     */

}
