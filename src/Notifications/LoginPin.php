<?php

namespace Empuxa\LoginViaPin\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * @todo test
 */
class LoginPin extends Notification
{
    public function __construct(private readonly string $pin, private readonly string $ip)
    {
    }

    /**
     * @param array<string> $notifiable
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
            'valid_until' => $notifiable->{config('login-via-pin.columns.pin_valid_until')},
            'pin'         => $this->pin,
            'ip'          => $this->ip,
        ];

        return (new MailMessage)
            ->subject(__('login-via-pin::notification.mail.subject', $params))
            ->greeting(__('login-via-pin::notification.mail.greeting', $params))
            ->line(__('login-via-pin::notification.mail.line-1', $params))
            ->line(__('login-via-pin::notification.mail.line-2', $params))
            ->line(__('login-via-pin::notification.mail.line-3', $params))
            ->action(
                __('login-via-pin::notification.mail.cta', $params),
                route('login-via-pin.pin.show'),
            )
            ->markdown('login-via-pin::notification', [
                'notifiable' => $notifiable,
                'pin'        => str_split($this->pin),
            ]);
    }
}
