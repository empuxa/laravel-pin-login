<?php

namespace Empuxa\PinLogin\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;

class CreateAndSendLoginPin
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public $user, public readonly string $ip = '')
    {
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $columns = config('pin-login.columns');
        $notification = config('pin-login.notification');
        $pin = self::createPin();

        $this->user->{$columns['pin']} = Hash::make($pin);
        $this->user->{$columns['pin_valid_until']} = now()->addSeconds(config('pin-login.pin.expires_in'));
        $this->user->saveQuietly();

        $this->user->notify(new $notification($pin, $this->ip));
    }

    /**
     * @throws \Exception
     */
    public static function createPin(): string
    {
        return str_pad(
            (string) random_int(0, (int) str_repeat('9', config('pin-login.pin.length'))),
            config('pin-login.pin.length'),
            '0',
            STR_PAD_LEFT,
        );
    }
}
