<?php

namespace App\Http\Controllers\listeners;

interface Listener
{
    public function listen(string $channel): string;

}
