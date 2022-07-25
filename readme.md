# Laravel Idempotent Middleware

This package makes it easy to add an indempotentcy to Laravel requests.

## Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

Install this package with Composer:

    composer require broken-titan/laravel-idempotency-middleware

## Configuration

There are three configuration values in use which all have defaults. They can be overwritten in config/idempotency.php.

- idempotency.expiration (default: 1440)
- idempotency.header (default: "Idempotency-Key")
- idempotency.methods (default: ["POST"])

You can also pass route-specific parameters in the routes file when setting the middleware.

## Usage

For ease of use, it is recommended that you add the middleware in your App\Http\Kernel.php file to $routeMiddleware.
```
    'idempotency' => \BrokenTitan\Idempotency\Middleware\Idempotency::class

```
You can set middleware for routes using the standard middleware assignment function.
```
    Route::apiResource("model", "ModelController", )->middleware("idempotency");
```
The idempotency middle parameters can be set on a per-route basis, overriding the configuration default.
```
    Route::apiResource("model", "ModelController", )->middleware("idempotency:X-Custom-Header,POST,100");
```

## Testing

A Docker compose file is included that allows you to run tests for this package.

## Security

If you discover any security issues that would affect existing users, please email contact@brokentitan.com instead of using the issue tracker.

## Contributing

Feel free to contribute to the package.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
