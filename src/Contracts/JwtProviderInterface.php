<?php

namespace Mvdstam\PhpJwtMiddleware\Contracts;

use Lcobucci\JWT\Token;
use Mvdstam\PhpJwtMiddleware\Exceptions\JwtProviderException;
use Psr\Http\Message\RequestInterface;

/**
 * Interface JwtProviderInterface
 */
interface JwtProviderInterface
{
    /**
     * @param RequestInterface $request
     * @throws JwtProviderException
     *
     * @return Token
     */
    public function getFromRequest(RequestInterface $request);
}
