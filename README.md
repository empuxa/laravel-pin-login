# Laravel Login via PIN

[![Latest Version on Packagist](https://img.shields.io/packagist/v/empuxa/login-via-pin.svg?style=flat-square)](https://packagist.org/packages/empuxa/login-via-pin)
[![Tests](https://img.shields.io/github/actions/workflow/status/empuxa/login-via-pin/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/empuxa/login-via-pin/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/empuxa/login-via-pin.svg?style=flat-square)](https://packagist.org/packages/empuxa/login-via-pin)

![Banner](https://banners.beyondco.de/Login%20via%20PIN.png?theme=light&packageManager=composer+require&packageName=empuxa%2Flogin-via-pin&pattern=architect&style=style_1&description=Goodbye+passwords%21&md=1&showWatermark=1&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)

Say goodbye to passwords and sign in via PIN instead.
This package provides a simple way to add a PIN login to your Laravel application.

*Why shouldn't I use a magic link solution?* you may ask yourself.
Well, this package intends to be used in addition to the existing login methods.
You can also support sign ins for users that either didn't set a password yet or don't have an email address (e.g. users that signed up with a phone number only).

![How it works](docs/animation.gif)

## Requirements

In addition to the usual Laravel requirements, this package relies on [Alpine.js](https://alpinejs.dev/).
If you're using [Laravel LiveWire](https://laravel-livewire.com/), you are already good to go.
Otherwise, ensure to include Alpine.js in your application.
Also, you need to have a notifiable user model.

## Installation

Install the package via composer:

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

The sign in process has three steps:
1. The user enters their email address, phone number, or any other defined identifier, and requests a PIN.
2. If the information is valid, a PIN is sent to the user (you might need to adjust the notification channel within the user model).
3. The user enters the PIN and might be logged in.

### Adjust the views

While the first steps were quite simple, now it's time to adjust the views.
They are kept as simple as possible (some might also say "ugly"), and can be found in `resources/views/vendor/login-via-pin`.

*Why aren't they beautiful?*
Everybody uses different layouts and frameworks for their applications.
You know your application best, so you can adjust the views to your needs.

### Change the notification
Within the views you've copied, you'll find a notification that's sent to the user.
You might want to adjust it to your needs.

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
