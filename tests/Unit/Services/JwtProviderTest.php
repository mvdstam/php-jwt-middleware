<?php

namespace Mvdstam\PhpJwtMiddleware\Tests\Unit\Services;

use Lcobucci\JWT\Token;
use Mockery;
use Mockery\Mock;
use Mvdstam\PhpJwtMiddleware\Exceptions\JwtProviderException;
use Mvdstam\PhpJwtMiddleware\Services\JwtProvider;
use Mvdstam\PhpJwtMiddleware\Tests\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * Class JwtProviderTest
 */
class JwtProviderTest extends TestCase
{
    /**
     * @var JwtProvider
     */
    protected $provider;

    /**
     * @var RequestInterface|Mock
     */
    protected $request;

    protected function setUp()
    {
        parent::setUp();

        $this->provider = new JwtProvider;
        $this->request = Mockery::mock(RequestInterface::class);
    }

    public function testGetFromRequestThrowsExceptionWhenAuthorizationHeaderIsMissing()
    {
        $this->expectException(JwtProviderException::class);
        $this->expectExceptionMessage('Missing authorization header');

        $this->request
            ->shouldReceive('hasHeader')
            ->with('Authorization')
            ->andReturn(false);

        $this->provider->getFromRequest($this->request);
    }

    public function testGetFromRequestThrowsExceptionWhenAuthorizationHeadersDoNotContainBearerToken()
    {
        $this->expectException(JwtProviderException::class);
        $this->expectExceptionMessage('Could not get JWT from request');

        $headers = [
            'not-a-token',
            'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.TJVA95OrM7E2cBab30RMHrHDcEfxjoYZgeFONFh7HgQ'
        ];

        $this->request
            ->shouldReceive('hasHeader')
            ->with('Authorization')
            ->andReturn(true);

        $this->request
            ->shouldReceive('getHeader')
            ->with('Authorization')
            ->andReturn($headers);

        $this->provider->getFromRequest($this->request);
    }

    public function testGetFromRequestReturnsTokenWhenJWTIsFound()
    {
        $headers = [
            'not-a-token',
            'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.TJVA95OrM7E2cBab30RMHrHDcEfxjoYZgeFONFh7HgQ'
        ];

        $this->request
            ->shouldReceive('hasHeader')
            ->with('Authorization')
            ->andReturn(true);

        $this->request
            ->shouldReceive('getHeader')
            ->with('Authorization')
            ->andReturn($headers);

        $token = $this->provider->getFromRequest($this->request);
        $this->assertInstanceOf(Token::class, $token);
    }



}
