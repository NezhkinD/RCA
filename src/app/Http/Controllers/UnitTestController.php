<?php


namespace App\Http\Controllers;


class UnitTestController extends Controller
{

    public function test(int $var): bool
    {
        return $var > 13;
    }
}
