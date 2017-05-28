<?php

namespace Mvdstam\PhpJwtMiddleware\Events;

use Symfony\Component\EventDispatcher\Event;
use Throwable;

/**
 * Class JwtErrorEvent
 */
class JwtErrorEvent extends Event
{
    const NAME = 'jwt.error';

    /**
     * @var Throwable
     */
    protected $throwable;

    /**
     * JwtErrorEvent constructor.
     * @param Throwable $throwable
     */
    public function __construct(Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    /**
     * @return Throwable
     */
    public function getError(): Throwable
    {
        return $this->throwable;
    }
}
