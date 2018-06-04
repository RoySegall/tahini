<?php

namespace App\Services;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * JWTTokenAuthenticator (Guard implementation).
 *
 * @see http://knpuniversity.com/screencast/symfony-rest4/jwt-guard-authenticator
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class Taliaz extends AbstractGuardAuthenticator
{

    public function supports(Request $request)
    {
        return false;
    }

    /**
     * Returns a decoded JWT token extracted from a request.
     *
     * {@inheritdoc}
     *
     * @return PreAuthenticationJWTUserToken
     *
     * @throws InvalidTokenException If an error occur while decoding the token
     * @throws ExpiredTokenException If the request token is expired
     */
    public function getCredentials(Request $request)
    {
    }

    /**
     * Returns an user object loaded from a JWT token.
     *
     * {@inheritdoc}
     *
     * @param PreAuthenticationJWTUserToken Implementation of the (Security) TokenInterface
     *
     * @throws \InvalidArgumentException If preAuthToken is not of the good type
     * @throws InvalidPayloadException   If the user identity field is not a key of the payload
     * @throws UserNotFoundException     If no user can be loaded from the given token
     */
    public function getUser($preAuthToken, UserProviderInterface $userProvider)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $authException)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return;
    }

    /**
     * {@inheritdoc}
     *
     * @return JWTAuthenticationFailureResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException If there is no pre-authenticated token previously stored
     */
    public function createAuthenticatedToken(UserInterface $user, $providerKey)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }

}