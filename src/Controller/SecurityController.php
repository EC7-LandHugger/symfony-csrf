<?php

namespace App\Controller;

use App\enum\Direction;
use App\Service\CsrfPolluter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    #[Route('/login', 'app_login')]
    public function login(Request $request, CsrfPolluter $csrfPolluter): Response
    {
        $submittedCsrfToken = null;
        $pollutedCsrfToken = null;
        $isPollutedCsrfTokenValid = false;
        $whichEndToPollute = mt_rand(1, 100) % 2 === 0 ? Direction::LEFT : Direction::RIGHT;

        if ($request->isMethod('POST')) {
            $submittedCsrfToken = $request->request->get('_csrf_token');
            $pollutedCsrfToken = $csrfPolluter->polluteCsrfToken($submittedCsrfToken, $whichEndToPollute);
            $isPollutedCsrfTokenValid = $this->isCsrfTokenValid('authenticate', $pollutedCsrfToken);
        }

        return $this->render('security/login.html.twig', [
            'submittedCsrfToken' => $submittedCsrfToken,
            'pollutedCsrfToken' => $pollutedCsrfToken,
            'isPollutedCsrfTokenValid' => $isPollutedCsrfTokenValid,
            'pollutedEnd' => $whichEndToPollute->value,
        ]);
    }
}
