<?php


namespace App\Http\Controllers\senders;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Redis;
use App\Http\Controllers\RedisSender\Sender;

class RedisSender extends Controller implements Sender
{

    public \Redis $redis;

    public string $key;

    public function __construct()
    {
        $this->redis = (new Redis)->connect;
    }

    public function send(string $channel, array $value): bool
    {
        $insert = '';
        if (array_key_exists('array', $value)) {
            $insert = json_encode($value['array'], JSON_THROW_ON_ERROR, 512);
        }

        if (array_key_exists('string', $value)) {
            $insert = $value['string'];
        }

        if ($insert === '') {
            return false;
        }

        $result = $this->redis->lPush($channel, $insert);

        if (!$result) {
            return false;
        }

        return true;
    }
}
