<?php

namespace App\Http\Controllers\handlers;

use App\Http\Controllers\captcha_solvers\RuCaptcha;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Redis;
use App\Http\Controllers\ServiceConfigurator;
use App\Http\Controllers\transformers\PolicyTransformer;
use App\rcaChecksList;
use Curl\Curl;
use DateTime;

class ChecksHandler extends Controller
{
    private Redis $redis;

    private Curl $curl;

    private RuCaptcha $rucaptcha;

    private PolicyTransformer $transformer;

    private rcaChecksList $checksListModel;

    private string $task;

    private string $channel;

    private string $dateTime;

    private string $requestURL;

    private string $requestSiteKey;

    private string $recaptchaV2Token;

    private array $check;

    private array $response;


    public function __construct(string $channel)
    {
        $this->redis = (new Redis());

        $this->dateTime = (new DateTime)->format('Y-m-d H:i:s.u');

        $this->curl = new Curl();

        $this->rucaptcha = new RuCaptcha();

        $this->transformer = new PolicyTransformer();

        $this->checksListModel = new rcaChecksList();

        $this->channel = $channel;

        $this->requestURL = ServiceConfigurator::CHECK_HANDLER_REQUEST_URL;

        $this->requestSiteKey = ServiceConfigurator::CHECK_HANDLER__SITE_KEY;
    }


    public function handle()
    {
        if ($this->redis->connect->lLen($this->channel) === 0) {
            return true;
        }

       $this
            ->getTask()
            ->getRecaptchaV2Token()
            ->makeRequestToRca()
            ->checkCaptchaSolution();

        return $this->response;
    }

    private function getTask(): ChecksHandler
    {
        $this->task = $this->redis->connect->lPop($this->channel);

        $task = $this->toArray($this->task);

        $check = $this->checksListModel->getUsingId($task['check']);

        $check = $this->objToArray($check);

        $this->check = $check[0];

        $this->checksListModel->updateUsingId($task['check'], ['status' => 'work']);

        $this->checksListModel->updateFieldUpdatedAt($task['check']);

        return $this;
    }

    private function getRecaptchaV2Token(): ChecksHandler
    {
        $task = $this->toArray($this->task);

        $rucaptcha = $this
            ->rucaptcha
            ->solveRecaptchaV2($this->requestURL, $this->requestSiteKey);

        if (!empty($rucaptcha->errors)) {
            $this->checksListModel->updateUsingId($task['check'], ['status' => 'wait']);
            $this->redis->connect->lPush($this->channel, $this->task);
            throw new \RuntimeException('Captcha not solved');
        }

        $this->recaptchaV2Token = $rucaptcha->result;

        return $this;
    }


    private function makeRequestToRca()
    {
        $check = $this->check;

        $body = $this->transformer->getBody($this->recaptchaV2Token, ['vin' => $check['value']]);

        $this->curl->setHeaders($this->transformer->getHeaders());
        $this->curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        //$this->response = $this->curl->post($this->requestURL, $body);

        $this->curl->post($this->requestURL, $body);

        $this->response = $this->objToArray($this->curl->response);

        return $this;
    }

    private function checkCaptchaSolution()
    {
        $task = $this->toArray($this->task);

        $response = $this->response;

        if (!$response['validCaptcha'] || !array_key_exists('validCaptcha', $response)) {
            $this->redis->connect->lPush($this->channel, $this->task);
            $this->checksListModel->updateUsingId($task['check'], ['status' => 'wait']);

            $message = [
                'message' => 'Captcha does not solved',
                'response' => $response
            ];
            throw new \RuntimeException(json_encode($message), 0);
        }

        $this->checksListModel->updateUsingId($task['check'], [
            'status' => 'done',
            'results' => json_encode($response, JSON_THROW_ON_ERROR, 512),
        ]);

        return $this;
    }

    private function completeCheck()
    {

    }

    private function objToArray($object)
    {
        $json = json_encode($object);

        return json_decode($json, true);
    }

    private function toArray(string $json)
    {
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    private function toJson(array $array)
    {
        return json_encode($array, 512, JSON_THROW_ON_ERROR);
    }
}
