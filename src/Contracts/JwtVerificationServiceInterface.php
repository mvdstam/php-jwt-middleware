<?php

namespace Mvdstam\PhpJwtMiddleware\Contracts;

use Lcobucci\JWT\Token;
use Mvdstam\PhpJwtMiddleware\Exceptions\JwtVerificationException;

/**
 * Interface JwtVerificationServiceInterface
 */
interface JwtVerificationServiceInterface
{
    /**
     * @param Token $token
     * @throws JwtVerificationException
     *
     * @return void
     */
    public function verify(Token $token);
}
