<?php

namespace App\Http\Controllers\captcha_solvers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class RuCaptcha extends Controller implements CaptchaSolver
{
    private string $urlIn = 'https://rucaptcha.com/in.php';

    private string $urlRes = 'https://rucaptcha.com/res.php';

    private int $maxWaitInSec = 150;

    private string $regexAnswer = '/(?<=OK\|).*/m';

    private Client $guzzle;

    private int $startTime;

    public string $rucaptchaTaskId = '';

    public string $result = '';

    public bool $status = false;

    public array $errors = [];


    public function __construct()
    {
        $this->guzzle = new Client();

        $this->startTime = time();
    }

    public function solveRecaptchaV2(string $url, string $siteKey): RuCaptcha
    {
        $response = $this->guzzle->request('POST', $this->urlIn, [
            'query' => [
                'key' => getenv('RUCAPTCHA_KEY'),
                'method' => 'userrecaptcha',
                'googlekey' => $siteKey,
                'pageurl' => $url,
            ]
        ])->getBody()->getContents();

        return $this
            ->getRucaptchaTaskId($response)
            ->getResult();
    }

    public function solveRecaptchaV3(string $url, string $siteKey): RuCaptcha
    {
        // TODO: Implement solveRecaptchaV3() method.
    }

    public function solveTextCaptcha(string $base64Image): RuCaptcha
    {
        // TODO: Implement solveTextCaptcha() method.
    }


    private function getRucaptchaTaskId(string $response): RuCaptcha
    {
        preg_match($this->regexAnswer, $response, $matches, PREG_UNMATCHED_AS_NULL);

        if (empty($matches)) {
            $this->errors = [
                'matches' => $matches,
                'message' => 'No match found',
                'subject' => $response,
            ];

            return $this;
        }

        $this->rucaptchaTaskId = $matches[0];

        return $this;
    }

    private function getResult(): RuCaptcha
    {
        $response = '';
        while (($this->startTime + $this->maxWaitInSec) > time()) {
            sleep(1);

            $response = $this->guzzle->request('POST', $this->urlRes, [
                'query' => [
                    'key' => getenv('RUCAPTCHA_KEY'),
                    'json' => 1,
                    'action' => 'get',
                    'id' => $this->rucaptchaTaskId,
                ]
            ])->getBody()->getContents();

            try {
                $array = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

            } catch (\JsonException $e) {
                $this->errors = [
                    'message' => 'Rucaptcha service error',
                    'exception' => $e->getMessage(),
                    'response' => $response
                ];
                return $this;
            }

            if (array_key_exists('status', $array) && $array['status'] === 1) {
                $this->result = $array['request'];
                $this->status = true;
                return $this;
            }
        }

        $this->errors = [
            'response' => $response,
            'message' => 'Time is up, captcha is not solved'
        ];
        return $this;
    }
}
