<?php


namespace App\Http\Controllers\transformers;


interface Transformer
{
    public function getBody(string $captchaToken, array $options): array;

    public function getHeaders(): array;
}
