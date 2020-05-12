<?php
namespace App\Http\Controllers;


class Redis extends Controller
{
    public \Redis $connect;

    public array $info;

    public function __construct()
    {
        $redis = new \Redis();

        $redis->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT'));

        $this->info = (array)$redis->info();

        $this->connect = $redis;

        $this->setConfiguration();
    }

    private function setConfiguration(): void
    {
        /* 
        * $parameter: 
        * hash-max-ziplist-entries 
        * set-max-intset-entries  //
        * zset-max-ziplist-entries 
        */

        $memoryLimit = getenv('REDIS_MEMORY_LIMIT_MB');

        if ($memoryLimit !== $this->info['maxmemory']) {
            $this->connect->config('SET', 'maxmemory', $memoryLimit);
        }
    }
}
