<?php
namespace App\Http\Controllers\captcha_solvers;


interface CaptchaSolver
{
    public function solveRecaptchaV2(string $url, string $siteKey): CaptchaSolver;

    public function solveRecaptchaV3(string $url, string $siteKey): CaptchaSolver;

    public function solveTextCaptcha(string $base64Image): CaptchaSolver;
}
