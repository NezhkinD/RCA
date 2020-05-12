<?php


namespace App\Http\Controllers\handlers;


interface Handler
{
    public function listen(string $channel): string;
}
