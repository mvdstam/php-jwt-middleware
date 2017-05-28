<?php

namespace Mvdstam\PhpJwtMiddleware\Services;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Token;
use Mvdstam\PhpJwtMiddleware\Contracts\JwtVerificationServiceInterface;
use Mvdstam\PhpJwtMiddleware\Exceptions\JwtVerificationException;

/**
 * Class JwtVerificationService
 */
class JwtVerificationService implements JwtVerificationServiceInterface
{
    /**
     * @var Signer
     */
    protected $signer;

    /**
     * @var null|string
     */
    protected $key;

    /**
     * JwtVerificationService constructor.
     * @param Signer $signer
     * @param string|null $key
     */
    public function __construct(Signer $signer, string $key = null)
    {
        $this->signer = $signer;
        $this->key = $key;
    }

    /**
     * @inheritdoc
     */
    public function verify(Token $token)
    {
        if ($token->isExpired()) {
            throw new JwtVerificationException('JWT has expired');
        }

        if ( ! $token->verify($this->signer, $this->key)) {
            throw new JwtVerificationException('JWT could not be verified');
        }
    }
}
