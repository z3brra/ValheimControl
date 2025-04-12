<?php


namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

use Psr\Log\LoggerInterface;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UserRepository $repository,
        private LoggerInterface $logger
    ) {}

    public function supports(Request $request): ?bool
    {
        $hasToken = $request->headers->has(key: 'X-AUTH-TOKEN');
        return $hasToken;
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get(key: 'X-AUTH-TOKEN');
        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException(message: 'No API token provided');
        }

        $user = $this->repository->findOneBy(['apiToken' => $apiToken]);
        if (null === $user) {
            throw new UserNotFoundException();
        }
        return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];
        return new JsonResponse(data: $data, status: Response::HTTP_UNAUTHORIZED);
    }

}
