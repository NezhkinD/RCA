<?php

namespace App\Http\Controllers\RedisSender;

interface Sender
{
    public function send(string $channel, array $value): bool;
}
