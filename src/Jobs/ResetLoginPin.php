<?php

namespace Empuxa\PinLogin\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResetLoginPin
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public $user)
    {
    }

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->user->{config('pin-login.columns.pin_valid_until')} = now()->subMinute();
        $this->user->saveQuietly();
    }
}
