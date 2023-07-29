<?php

namespace Empuxa\PinLogin\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * @todo test
 */
class LoginPin extends Notification
{
    public function __construct(protected readonly string $pin, protected readonly string $ip)
    {
    }

    /**
     * @param  array<string>  $notifiable
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $params = [
            'app'         => config('app.name'),
            'name'        => $notifiable->name,
            'valid_until' => $notifiable->{config('pin-login.columns.pin_valid_until')},
            'pin'         => $this->pin,
            'ip'          => $this->ip,
        ];

        return (new MailMessage)
            ->subject(__('pin-login::notification.mail.subject', $params))
            ->greeting(__('pin-login::notification.mail.greeting', $params))
            ->line(__('pin-login::notification.mail.line-1', $params))
            ->line(__('pin-login::notification.mail.line-2', $params))
            ->line(__('pin-login::notification.mail.line-3', $params))
            ->action(
                __('pin-login::notification.mail.cta', $params),
                route('pin-login.pin.form'),
            )
            ->markdown('pin-login::notification', [
                'notifiable' => $notifiable,
                'pin'        => str_split($this->pin),
            ]);
    }
}
