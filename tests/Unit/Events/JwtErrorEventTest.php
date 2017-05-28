<?php

namespace Mvdstam\PhpJwtMiddleware\Tests\Unit\Events;

use Exception;
use Mockery;
use Mvdstam\PhpJwtMiddleware\Events\JwtErrorEvent;
use Mvdstam\PhpJwtMiddleware\Tests\TestCase;
use Throwable;

/**
 * Class JwtErrorEventTest
 */
class JwtErrorEventTest extends TestCase
{
    /**
     * @var JwtErrorEvent
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
        $this->event = new JwtErrorEvent($this->throwable);
    }

    public function testGetErrorReturnsThrowableInstance()
    {
        $this->assertSame($this->throwable, $this->event->getError());
    }
}
