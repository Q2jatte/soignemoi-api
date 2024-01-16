<?php

namespace App\Service;

use App\Captcha\GoogleReCaptchaChecker;

class CaptchaChecker
{
    public function check(string $secret, string $captchaValue): bool
    {
        $checker = new GoogleReCaptchaChecker($secret);
        return $checker->check($captchaValue);
    }
}