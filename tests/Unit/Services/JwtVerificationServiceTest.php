<?php

namespace Mvdstam\PhpJwtMiddleware\Tests\Unit\Services;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Token;
use Mockery;
use Mockery\Mock;
use Mvdstam\PhpJwtMiddleware\Exceptions\JwtVerificationException;
use Mvdstam\PhpJwtMiddleware\Services\JwtVerificationService;
use Mvdstam\PhpJwtMiddleware\Tests\TestCase;

/**
 * Class JwtVerificationServiceTest
 */
class JwtVerificationServiceTest extends TestCase
{

    /**
     * @var JwtVerificationService
     */
    protected $verificationService;

    /**
     * @var Signer|Mock
     */
    protected $signer;

    protected function setUp()
    {
        parent::setUp();

        $this->signer = Mockery::mock(Signer::class);
        $this->verificationService = new JwtVerificationService($this->signer);
    }

    public function testVerifyThrowsExceptionWhenJwtIsExpired()
    {
        /** @var Token|Mock $token */
        $token = Mockery::mock(Token::class);

        $token
            ->shouldReceive('isExpired')
            ->once()
            ->andReturn(true);

        $this->expectException(JwtVerificationException::class);
        $this->expectExceptionMessage('JWT has expired');

        $this->verificationService->verify($token);
    }

    public function testVerifyThrowsExceptionWhenJWTIsNotVerified()
    {
        /** @var Token|Mock $token */
        $token = Mockery::mock(Token::class);

        $token
            ->shouldReceive('isExpired')
            ->once()
            ->andReturn(false);

        $token
            ->shouldReceive('verify')
            ->with($this->signer, null)
            ->andReturn(false);

        $this->expectException(JwtVerificationException::class);
        $this->expectExceptionMessage('JWT could not be verified');

        $this->verificationService->verify($token);
    }

    public function testVerifyReturnsVoidWhenJWTIsValid()
    {
        /** @var Token|Mock $token */
        $token = Mockery::mock(Token::class);

        $token
            ->shouldReceive('isExpired')
            ->once()
            ->andReturn(false);

        $token
            ->shouldReceive('verify')
            ->with($this->signer, null)
            ->andReturn(true);

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $this->assertNull($this->verificationService->verify($token));
    }
}
