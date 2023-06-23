<?php

namespace Empuxa\LoginViaPin\Jobs;

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
        $columns = config('login-via-pin.columns');
        $notification = config('login-via-pin.notification');
        $pin = self::createPin();

        $this->user->{$columns['pin']} = Hash::make($pin);
        $this->user->{$columns['pin_valid_until']} = now()->addSeconds(config('login-via-pin.pin.expires_in'));
        $this->user->saveQuietly();

        $this->user->notify(new $notification($pin, $this->ip));
    }

    /**
     * @throws \Exception
     */
    public static function createPin(): string
    {
        return str_pad(
            (string) random_int(0, (int) str_repeat('9', config('login-via-pin.pin.length'))),
            config('login-via-pin.pin.length'),
            '0',
            STR_PAD_LEFT,
        );
    }
}
