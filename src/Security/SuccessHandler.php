<?php

namespace App\Security;

use Drenso\OidcBundle\Model\OidcTokens;
use Drenso\OidcBundle\Security\Token\OidcToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

/**
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 */
class SuccessHandler extends DefaultAuthenticationSuccessHandler
{
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        /** @var OidcTokens $oidcTokens */
        $oidcTokens = $token->getAttribute(OidcToken::AUTH_DATA_ATTR);

        // store access token into session
        $request->getSession()->set('accessToken', $oidcTokens->getAccessToken());

        return parent::onAuthenticationSuccess($request, $token);
    }
}