<?php

namespace App\Services;


class UrlService
{
    public function generate(string $routeName ,array $parameters ,int $minutes = 30):string
    {
        return \Illuminate\Support\Facades\URL::temporarySignedRoute($routeName,$minutes,$parameters);
    }
}
