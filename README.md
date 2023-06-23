# Login via PIN

[![Latest Version on Packagist](https://img.shields.io/packagist/v/empuxa/login-via-pin.svg?style=flat-square)](https://packagist.org/packages/empuxa/login-via-pin)
[![Tests](https://img.shields.io/github/actions/workflow/status/empuxa/login-via-pin/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/empuxa/login-via-pin/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/empuxa/login-via-pin.svg?style=flat-square)](https://packagist.org/packages/empuxa/login-via-pin)

Say goodbye to passwords and sign in via PIN instead.
This package provides a simple way to add a PIN login to your Laravel application.
However, it's not a replacement for existing magic link packages, since you still need to manually enter the PIN.

## Requirements

In addition to the usual Laravel requirements, this package relies on Alpine.js.
If you're using Laravel LiveWire, you are already good to go.
Otherwise ensure to include Alpine.js in your application.
Also, you need to have a user model with an email address and a `remember_token` column.

## Installation

You can install the package via composer:

```bash
composer require empuxa/login-via-pin
```

Afterward, copy the vendor files and adjust the config file `config/login-via-pin.php` to your needs:

```bash
php artisan vendor:publish --provider="Empuxa\LoginViaPin\ServiceProvider"
```

Finally, run the migrations:

```bash
php artisan migrate
```

## Usage

The sign in process is split into these steps:
1. The user enters their email address and requests a PIN.
    - fires an event: `Empuxa\LoginViaPin\Events\LoginPinRequested`
2. If the email address is valid, a PIN is sent to the user.
3. The user enters the PIN and might be logged in.
   - fires an event: `Empuxa\LoginViaPin\Events\LoggedIn`

### Adjust the views
Now that you've installed the package, you can add the login form to your application.
Go to the copied views and adjust them to your needs. 
They're located in `resources/views/vendor/login-via-pin` and kept as simple as possible.

### Change the notification
Within the views you've copied, you'll find a notification that's sent to the user.
You might want to adjust it to your needs.

### Events


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Marco Raddatz](https://github.com/marcoraddatz)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
