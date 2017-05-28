<?php

namespace Mvdstam\PhpJwtMiddleware\Services;

use Exception;
use Lcobucci\JWT\Parser;
use Mvdstam\PhpJwtMiddleware\Contracts\JwtProviderInterface;
use Mvdstam\PhpJwtMiddleware\Exceptions\JwtProviderException;
use Psr\Http\Message\RequestInterface;

/**
 * Class JwtProviderService
 */
class JwtProvider implements JwtProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getFromRequest(RequestInterface $request)
    {
        if ( ! $request->hasHeader('Authorization')) {
            throw new JwtProviderException('Missing authorization header');
        }

        foreach($request->getHeader('Authorization') as $authorizationHeader) {
            if (preg_match('/^Bearer (.+)$/', $authorizationHeader, $matches)) {
                return (new Parser)->parse($matches[1]);
            }
        }

        throw new JwtProviderException('Could not get JWT from request');
    }
}
