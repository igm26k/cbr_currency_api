<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TokenAuthenticator extends AbstractAuthenticator
{
    protected const HEADER_AUTH_TOKEN = 'AuthToken';
    protected const HEADER_EMAIL      = 'UserEmail';

    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): ?bool
    {
        return $request->headers->has(self::HEADER_AUTH_TOKEN) && $request->headers->has(self::HEADER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get(self::HEADER_AUTH_TOKEN);
        $email = $request->headers->get(self::HEADER_EMAIL);

        if (empty($token)) {
            throw new CustomUserMessageAuthenticationException('Access token not found (header: "{{ header }}")', [
                '{{ header }}' => self::HEADER_AUTH_TOKEN,
            ]);
        }

        if (empty($email)) {
            throw new CustomUserMessageAuthenticationException('Email not found (header: "{{ header }}")', [
                '{{ header }}' => self::HEADER_EMAIL,
            ]);
        }

        $userRepository = new UserRepository($this->doctrine);
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $factory = new PasswordHasherFactory(['common' => ['algorithm' => 'bcrypt']]);
        $passwordHasher = $factory->getPasswordHasher('common');
        $isValidToken = $passwordHasher->verify($user->getAccessToken(), $token);

        return new SelfValidatingPassport(new UserBadge($isValidToken ? $email : ''));
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        throw $exception;
    }
}