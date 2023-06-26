<?php

namespace Empuxa\PinLogin\Requests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guest();
    }

    public function getUserModel(string $identifier = null): ?Model
    {
        $query = config('pin-login.model')::query();

        // If the model has a dedicated scope for the pin login, we will use it.
        if (method_exists(config('pin-login.model'), 'pinLoginScope')) {
            $query = config('pin-login.model')::pinLoginScope();
        }

        return $query
            ->where(
                config('pin-login.columns.identifier'),
                $identifier ?? $this->input(config('pin-login.columns.identifier')),
            )
            ->first();
    }
}
