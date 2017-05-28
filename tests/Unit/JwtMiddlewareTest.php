<?php

namespace Mvdstam\PhpJwtMiddleware\Tests\Unit;

use Exception;
use Lcobucci\JWT\Token;
use Mockery;
use Mockery\Mock;
use Mvdstam\PhpJwtMiddleware\Contracts\JwtProviderInterface;
use Mvdstam\PhpJwtMiddleware\Contracts\JwtVerificationServiceInterface;
use Mvdstam\PhpJwtMiddleware\Events\JwtErrorEvent;
use Mvdstam\PhpJwtMiddleware\Events\JwtInvalidEvent;
use Mvdstam\PhpJwtMiddleware\Events\JwtValidEvent;
use Mvdstam\PhpJwtMiddleware\Exceptions\JwtBaseException;
use Mvdstam\PhpJwtMiddleware\Exceptions\JwtProviderException;
use Mvdstam\PhpJwtMiddleware\Exceptions\JwtVerificationException;
use Mvdstam\PhpJwtMiddleware\JwtMiddleware;
use Mvdstam\PhpJwtMiddleware\Tests\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class JwtMiddlewareTest
 */
class JwtMiddlewareTest extends TestCase
{
    /**
     * @var JwtMiddleware
     */
    protected $middleware;

    /**
     * @var JwtVerificationServiceInterface|Mock
     */
    protected $verificationService;

    /**
     * @var JwtProviderInterface|Mock
     */
    protected $provider;

    /**
     * @var EventDispatcherInterface|Mock
     */
    protected $eventDispatcher;

    /**
     * @var ServerRequestInterface|Mock
     */
    protected $request;

    /**
     * @var ResponseInterface|Mock
     */
    protected $response;

    /**
     * @var Token
     */
    protected $token;

    protected function setUp()
    {
        parent::setUp();

        $this->verificationService = Mockery::mock(JwtVerificationServiceInterface::class);
        $this->provider = Mockery::mock(JwtProviderInterface::class);
        $this->eventDispatcher = Mockery::mock(EventDispatcherInterface::class);
        $this->token = Mockery::mock(Token::class);
        $this->request = Mockery::mock(ServerRequestInterface::class);
        $this->response = Mockery::mock(ResponseInterface::class);

        $this->middleware = new JwtMiddleware(
            $this->verificationService,
            $this->provider,
            $this->eventDispatcher
        );
    }

    public function testMiddlewareCallsNextCallableWhenJwtIsValidAndReturnsResponse()
    {
        $this->provider
            ->shouldReceive('getFromRequest')
            ->once()
            ->with($this->request)
            ->andReturn($this->token);

        $this->verificationService
            ->shouldReceive('verify')
            ->once()
            ->with($this->token);

        $this->eventDispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->with(JwtValidEvent::NAME, equalTo(new JwtValidEvent($this->token)));

        $this->request
            ->shouldReceive('withAttribute')
            ->once()
            ->with('jwt', $this->token)
            ->andReturnSelf();

        $newResponse = Mockery::mock(ResponseInterface::class);

        $next = function(ServerRequestInterface $request, ResponseInterface $response) use ($newResponse) {
            return $newResponse;
        };

        $response = $this->middleware->__invoke($this->request, $this->response, $next);
        $this->assertSame($newResponse, $response);
    }

    public function testMiddlewareCallsNextCallableWhenJwtIsValidAndReturnsResponseWithoutEventDispatcher()
    {
        $middleware = new JwtMiddleware($this->verificationService, $this->provider);

        $this->provider
            ->shouldReceive('getFromRequest')
            ->once()
            ->with($this->request)
            ->andReturn($this->token);

        $this->verificationService
            ->shouldReceive('verify')
            ->once()
            ->with($this->token);

        $this->eventDispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->with(JwtValidEvent::NAME, equalTo(new JwtValidEvent($this->token)));

        $this->request
            ->shouldReceive('withAttribute')
            ->once()
            ->with('jwt', $this->token)
            ->andReturnSelf();

        $newResponse = Mockery::mock(ResponseInterface::class);

        $next = function(ServerRequestInterface $request, ResponseInterface $response) use ($newResponse) {
            return $newResponse;
        };

        $response = $middleware->__invoke($this->request, $this->response, $next);
        $this->assertSame($newResponse, $response);
    }

    public function testMiddlewareReturnsHttpUnauthorizedWhenTokenCouldNotBeVerified()
    {
        /** @var $newResponse ResponseInterface|Mock */
        $newResponse = Mockery::mock(ResponseInterface::class);

        $this->provider
            ->shouldReceive('getFromRequest')
            ->once()
            ->with($this->request)
            ->andReturn($this->token);

        $this->verificationService
            ->shouldReceive('verify')
            ->once()
            ->with($this->token)
            ->andThrow(JwtVerificationException::class);

        $this->eventDispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->with(JwtInvalidEvent::NAME, any(JwtInvalidEvent::class));

        $this->response
            ->shouldReceive('withStatus')
            ->once()
            ->with(401)
            ->andReturn($newResponse);

        $response = $this->middleware->__invoke($this->request, $this->response);
        $this->assertSame($newResponse, $response);
    }

    public function testMiddlewareReturnsHttpUnauthorizedWhenTokenCouldNotBeProvider()
    {
        /** @var $newResponse ResponseInterface|Mock */
        $newResponse = Mockery::mock(ResponseInterface::class);

        $this->provider
            ->shouldReceive('getFromRequest')
            ->once()
            ->with($this->request)
            ->andThrow(JwtProviderException::class);

        $this->eventDispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->with(JwtInvalidEvent::NAME, any(JwtInvalidEvent::class));

        $this->response
            ->shouldReceive('withStatus')
            ->once()
            ->with(401)
            ->andReturn($newResponse);

        $response = $this->middleware->__invoke($this->request, $this->response);
        $this->assertSame($newResponse, $response);
    }

    public function testMiddlewareReturnsHttpInternalServerErrorWhenAnUnexpectedExceptionWasThrown()
    {
        /** @var $newResponse ResponseInterface|Mock */
        $newResponse = Mockery::mock(ResponseInterface::class);

        $this->provider
            ->shouldReceive('getFromRequest')
            ->once()
            ->with($this->request)
            ->andReturn($this->token);

        $this->verificationService
            ->shouldReceive('verify')
            ->once()
            ->with($this->token)
            ->andThrow(Exception::class);

        $this->eventDispatcher
            ->shouldReceive('dispatch')
            ->once()
            ->with(JwtErrorEvent::NAME, any(JwtErrorEvent::class));

        $this->response
            ->shouldReceive('withStatus')
            ->once()
            ->with(500)
            ->andReturn($newResponse);

        $response = $this->middleware->__invoke($this->request, $this->response);
        $this->assertSame($newResponse, $response);
    }
}
