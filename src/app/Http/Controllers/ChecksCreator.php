<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Channels\RedisChannels;
use App\Http\Controllers\senders\RedisSender;
use App\rcaChecksList;
use App\rcaChecksLogs;
use App\rcaNumbers;
use DateTime;


class ChecksCreator extends Controller
{
    private string $content;

    private string $dateTime;

    private RedisSender $redis;

    private array $insert = [];

    private array $errors = [];

    private array $numbers = [];

    private array $checks = [];

    private array $logs = [];

    public function __construct(string $content)
    {
        $this->content = $content;

        $this->dateTime = (new DateTime)->format('Y-m-d H:i:s.u');

        $this->redis = new RedisSender();
    }


    public function createChecks()
    {
        try {
            $this->verifyArray()
                ->insertInNumbers()
                ->insertInChecksList()
                ->insertInChecksLogs()
                ->insertInRedisList();

        } catch (\JsonException $e) {
            return [
                'result' => false,
                'errors' => 'wrong json',
            ];
        }

        if (empty($this->errors)) {
            return [
                'result' => true,
            ];
        }

        return [
            'result' => false,
            'errors' => $this->errors,
        ];
    }

    /** <h2> Проверить входящий массив на корректность </h2>
     * @return ChecksCreator
     * @throws \JsonException
     */
    private function verifyArray(): ChecksCreator
    {
        $array = json_decode($this->content, true, 512, JSON_THROW_ON_ERROR);

        foreach ($array as $key => $item) {

            if (!array_key_exists('type', $item)) {
                $this->errors[] = [
                    'element' => $key,
                    'message' => 'не найден type'
                ];
                continue;
            }

            if (!array_key_exists('value', $item)) {
                $this->errors[] = [
                    'element' => $key,
                    'message' => 'не найден value'
                ];
                continue;
            }

            if (!preg_match(ServiceConfigurator::CHECKS_CREATOR__REQUEST_REGEX, $item['type'])) {
                $this->errors[] = [
                    'element' => $key,
                    'message' => 'type указан неверно, используйте одно из указанных значений: vin|number|body|chassis'
                ];
                continue;
            }

            $this->insert[] = [
                'value' => trim($item['value']),
                'type' => $item['type'],
                'uuid' => $item['uuid'],
                'created_at' => $this->dateTime
            ];
        }

        return $this;
    }

    /**
     * @return ChecksCreator
     */
    private function insertInNumbers(): ChecksCreator
    {
        $this->numbers = (new rcaNumbers)->insertAndGetIds($this->insert);

        return $this;
    }

    /**
     * @return ChecksCreator
     */
    private function insertInChecksList(): ChecksCreator
    {
        $data = [];
        foreach ($this->numbers as $item) {
            $data[] = [
                'number' => $item->id,
                'created_at' => $this->dateTime,
                'updated_at' => $this->dateTime,
                'results' => '{}',
                'status' => 'wait',
            ];
        }

        $this->checks = (new rcaChecksList())->insertAndGetIds($data);

        return $this;
    }

    /**
     * @return ChecksCreator
     */
    private function insertInChecksLogs(): ChecksCreator
    {
        $data = [];
        foreach ($this->checks as $item) {
            $data[] = [
                'check' => $item->id,
                'created_at' => $item->created_at,
                'updated_at' => $item->created_at
            ];
        }

        $this->logs = (new rcaChecksLogs())->insertAndGetIds($data);

        return $this;
    }

    private function insertInRedisList(): void
    {
        foreach ($this->logs as $log) {
            $insert = [
                'check' => $log->check,
                'log' => $log->id
            ];

            $this->redis->send(RedisChannels::CHECKS_LIST, ['array' => $insert]);
        }
    }
}
