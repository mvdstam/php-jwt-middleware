<?php

namespace Mvdstam\PhpJwtMiddleware\Events;

use Lcobucci\JWT\Token;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class JwtValidEvent
 */
class JwtValidEvent extends Event
{
    const NAME = 'jwt.valid';

    /**
     * @var Token
     */
    protected $token;

    /**
     * JwtValidEvent constructor.
     * @param Token $token
     */
    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
    }
}
