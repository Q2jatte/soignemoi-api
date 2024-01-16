<?php

namespace App\Controller;

use App\Captcha\CaptchaCheckerInterface;
use App\Captcha\GoogleReCaptchaChecker;
use App\Service\CaptchaChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CaptchaController extends AbstractController
{
    #[Route('/api/recaptchaverify', name: 'verifyRecaptcha', methods: ['POST'])]
    public function verifyRecaptcha(Request $request, CaptchaChecker $captchaChecker): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Vérification de la présence des clés 'secret' et 'captcha' dans les données
        if (!isset($data['secret']) || !isset($data['captcha'])) {
            return new JsonResponse(['error' => 'Les clés "secret" et "captcha" sont requises.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $secret= $data['secret'];
        $captchaValue = $data['captcha'];
        
        // Vérification du format des données
        if (empty($secret) || empty($captchaValue)) {
            return new JsonResponse(['error' => 'Les valeurs de "secret" et "captcha" ne peuvent pas être vides.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Vérification du reCAPTCHA
        $isCaptchaValid = $captchaChecker->check($secret, $captchaValue);

        // Retourne une réponse JSON en fonction du résultat de la vérification
        return new JsonResponse(['success' => $isCaptchaValid]);

    }
}