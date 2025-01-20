<?php

namespace App\Controller;

use Drenso\OidcBundle\Exception\OidcCodeChallengeMethodNotSupportedException;
use Drenso\OidcBundle\Exception\OidcConfigurationException;
use Drenso\OidcBundle\Exception\OidcConfigurationResolveException;
use Drenso\OidcBundle\OidcClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class AuthenticationController
 *
 * @package App\Controller
 * @category Controller
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license Open Web Purge
 * @copyright GFP Tech 2024
 */
class AuthenticationController extends AbstractController
{
    /**
     * @throws OidcConfigurationException
     * @throws OidcConfigurationResolveException
     * @throws OidcCodeChallengeMethodNotSupportedException
     */
    #[Route('/sign-in', name: 'sign-in')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function signIn(OidcClientInterface $oidcClient): RedirectResponse
    {
        return $oidcClient->generateAuthorizationRedirect(null, ['openid', 'user_identity']);
    }

    #[Route('/auto-sign-in', name: 'auto-sign-in')]
    #[IsGranted('PUBLIC_ACCESS')]
    public function autoSignIn(Request $request): Response
    {
        return new Response('Une erreur s\'est produite : ' . $request->get('error'));
    }
}