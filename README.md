# PHP JWT Middleware

This package allows developers to add simple but powerful middleware to their application which does one thing, and one thing only: **validate incoming JWT's**.

## Requirements

- PHP 7 or above
- A package that provides [psr/http-message-implementation](https://packagist.org/providers/psr/http-message-implementation)

## Goal of this package

This package aims to provide the developer with a PSR-7 compliant HTTP middleware class that ensures the validity and integrity of incoming JWT's. When a JWT is found valid, it is simply added to the request as an attribute. Within the application, the JWT can simply be accessed using the current `ServerRequestInterface` implementation for your application:

```php
class MyController
{
    public function indexAction(ServerRequestInterface $request)
    {
        $jwt = $request->getAttribute('jwt'); // Instance of Lcobucci\JWT\Token 
    }
}
```
