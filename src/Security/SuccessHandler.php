<?php

namespace App\Security;

use Drenso\OidcBundle\Security\Token\OidcToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

/**
 * Class SuccessHandler
 *
 * @package App\Authenticator
 * @category
 * @author Benjamin Ghenne <benjamin.ghenne@gfptech.fr>
 * @license
 * @copyright GFP Tech 2023
 */
class SuccessHandler extends DefaultAuthenticationSuccessHandler
{
    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        // store accessToken into session
        $request->getSession()->set('accessToken', $token->getAttribute(OidcToken::AUTH_DATA_ATTR)->getAccessToken());

        return parent::onAuthenticationSuccess($request, $token);
    }
}