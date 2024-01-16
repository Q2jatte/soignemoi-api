<?php

namespace App\Captcha;

use GuzzleHttp\Client;

class GoogleReCaptchaChecker implements CaptchaCheckerInterface
{
    protected $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * {@inheritDoc}
     */
    public function check(string $captchaValue): bool
    {
        $data = $this->getCaptchaResponse($captchaValue);

        // Better checks could be done here
        if ($data && isset($data['success']) && true === $data['success']) {
            return true;
        }

        return false;
    }

    private function getCaptchaResponse($captchaValue): array
    {
        $response = $this->getClient()->request(
            'POST',
            'recaptcha/api/siteverify',
            [
                'form_params' => [
                    'secret'   => $this->secret,
                    'response' => $captchaValue,
                ],
            ]
        );

        return json_decode($response->getBody(), true);
    }

    private function getClient(): Client
    {
        return new Client([
            'base_uri' => 'https://www.google.com',
        ]);
    }
}