<?php
namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\PreAuthenticationJWTUserToken;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenAuthenticator extends JWTTokenAuthenticator
{
    /**
     * @param PreAuthenticationJWTUserToken $preAuthToken
     * @param UserProviderInterface $userProvider
     * @return null|\Symfony\Component\Security\Core\User\UserInterface
     */
    public function getUser($preAuthToken, UserProviderInterface $userProvider)
    {
        $user = parent::getUser($preAuthToken, $userProvider);

        // Expire token if password change
        if ($user->getPasswordChangeTimestamp() && $preAuthToken->getPayload()['iat'] < $user->getPasswordChangeTimestamp()) {
            throw new ExpiredTokenException();
        }

        return $user;
    }
}
