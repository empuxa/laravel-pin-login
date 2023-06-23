<?php

namespace Empuxa\LoginViaPin\Jobs;

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
        $this->user->{config('login-via-pin.columns.pin_valid_until')} = now()->subMinute();
        $this->user->saveQuietly();
    }
}
