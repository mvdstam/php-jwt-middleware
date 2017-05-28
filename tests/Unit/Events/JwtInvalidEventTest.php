<?php

namespace Mvdstam\PhpJwtMiddleware\Tests\Unit\Events;

use Exception;
use Mockery;
use Mvdstam\PhpJwtMiddleware\Events\JwtInvalidEvent;
use Mvdstam\PhpJwtMiddleware\Tests\TestCase;
use Throwable;

/**
 * Class JwtInvalidEventTest
 */
class JwtInvalidEventTest extends TestCase
{
    /**
     * @var JwtInvalidEvent
     */
    protected $event;

    /**
     * @var Throwable
     */
    protected $throwable;

    protected function setUp()
    {
        parent::setUp();

        $this->throwable = Mockery::mock(Exception::class);
        $this->event = new JwtInvalidEvent($this->throwable);
    }

    public function testGetErrorReturnsThrowableInstance()
    {
        $this->assertSame($this->throwable, $this->event->getError());
    }
}
