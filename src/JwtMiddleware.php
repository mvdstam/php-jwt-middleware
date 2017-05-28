<?php

namespace Mvdstam\PhpJwtMiddleware;

use Mvdstam\PhpJwtMiddleware\Contracts\JwtProviderInterface;
use Mvdstam\PhpJwtMiddleware\Contracts\JwtVerificationServiceInterface;
use Mvdstam\PhpJwtMiddleware\Events\JwtErrorEvent;
use Mvdstam\PhpJwtMiddleware\Events\JwtInvalidEvent;
use Mvdstam\PhpJwtMiddleware\Events\JwtValidEvent;
use Mvdstam\PhpJwtMiddleware\Exceptions\JwtBaseException;
use Mvdstam\PhpJwtMiddleware\Services\JwtProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

/**
 * Class JwtMiddleware
 */
class JwtMiddleware
{
    /**
     * @var JwtProviderInterface
     */
    protected $jwtProvider;

    /**
     * @var JwtVerificationServiceInterface
     */
    protected $jwtVerificationService;

    /**
     * @var EventDispatcherInterface|null
     */
    protected $eventDispatcher;

    /**
     * JWTMiddleware constructor.
     * @param JwtVerificationServiceInterface $jwtVerificationService
     * @param JwtProviderInterface $jwtProvider
     * @param EventDispatcherInterface|null $eventDispatcher
     */
    public function __construct(
        JwtVerificationServiceInterface $jwtVerificationService,
        JwtProviderInterface $jwtProvider,
        EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->jwtVerificationService = $jwtVerificationService;
        $this->jwtProvider = $jwtProvider;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next = null
    ) {
        try {
            $jwt = $this->jwtProvider->getFromRequest($request);
            $this->jwtVerificationService->verify($jwt);
            $this->dispatchEvent(JwtValidEvent::NAME, new JwtValidEvent($jwt));
        } catch (JwtBaseException $e) {
            $this->dispatchEvent(JwtInvalidEvent::NAME, new JwtInvalidEvent($e));

            return $response->withStatus(401);
        } catch (Throwable $e) {
            $this->dispatchEvent(JwtErrorEvent::NAME, new JwtErrorEvent($e));

            return $response->withStatus(500);
        }

        if ($next) {
            $response = call_user_func($next, $request->withAttribute('jwt', $jwt), $response);
        }

        return $response;
    }

    /**
     * @param string $eventName
     * @param Event|null $event
     */
    protected function dispatchEvent(string $eventName, Event $event = null)
    {
        if ( ! $this->eventDispatcher) {
            return;
        }

        $this->eventDispatcher->dispatch($eventName, $event);
    }
}
