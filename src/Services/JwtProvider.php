<?php

namespace Mvdstam\PhpJwtMiddleware\Services;

use Lcobucci\JWT\Parser;
use Mvdstam\PhpJwtMiddleware\Contracts\JwtProviderInterface;
use Mvdstam\PhpJwtMiddleware\Exceptions\JwtBadSyntaxException;
use Mvdstam\PhpJwtMiddleware\Exceptions\JwtProviderException;
use Psr\Http\Message\RequestInterface;
use Throwable;

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
                try {
                    return (new Parser)->parse($matches[1]);
                } catch (Throwable $e) {
                    throw new JwtBadSyntaxException('Could not parse JWT', 0, $e);
                }
            }
        }

        throw new JwtProviderException('Could not get JWT from request');
    }
}
