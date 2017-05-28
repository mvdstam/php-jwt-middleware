<?php

namespace Mvdstam\PhpJwtMiddleware\Tests\Unit\Events;

use Lcobucci\JWT\Token;
use Mvdstam\PhpJwtMiddleware\Events\JwtValidEvent;
use Mvdstam\PhpJwtMiddleware\Tests\TestCase;

/**
 * Class JwtValidEventTest
 */
class JwtValidEventTest extends TestCase
{
    /**
     * @var JwtValidEvent
     */
    protected $event;

    /**
     * @var Token
     */
    protected $token;

    protected function setUp()
    {
        parent::setUp();

        $this->token = \Mockery::mock(Token::class);
        $this->event = new JwtValidEvent($this->token);
    }

    public function testGetTokenReturnsToken()
    {
        $this->assertSame($this->token, $this->event->getToken());
    }
}
