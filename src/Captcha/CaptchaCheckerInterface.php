<?php

namespace App\Captcha;

use GuzzleHttp\Client;

interface CaptchaCheckerInterface
{
    /**
     * Checks captcha response
     *
     * @param string $captchaResponse
     * @return bool
     */
    public function check(string $captchaResponse): bool;
}