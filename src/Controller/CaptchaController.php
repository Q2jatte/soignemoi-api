<?php
// MANAGE GOOGLE RECAPTCHA

namespace App\Controller;

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
        
        // Check for the presence of 'secret' and 'captcha' keys in the data
        if (!isset($data['secret']) || !isset($data['captcha'])) {
            return new JsonResponse(['error' => 'Les clés "secret" et "captcha" sont requises.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $secret= $data['secret'];
        $captchaValue = $data['captcha'];
        
        // Check the format of the data
        if (empty($secret) || empty($captchaValue)) {
            return new JsonResponse(['error' => 'Les valeurs de "secret" et "captcha" ne peuvent pas être vides.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Verify the reCAPTCHA
        $isCaptchaValid = $captchaChecker->check($secret, $captchaValue);

        // Return a JSON response based on the verification result
        return new JsonResponse(['success' => $isCaptchaValid]);
    }
}