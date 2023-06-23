<?php

namespace Empuxa\LoginViaPin\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * @todo test
 * @todo i18n
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
            'name' => $notifiable->name,
            'valid_until' => $notifiable->{config('login-via-pin.columns.pin_valid_until')},
            'pin' => $this->pin,
            'ip' => $this->ip,
        ];

        // This notification uses a custom markdown template.
        // Therefore, the order of lines is different from the other notifications.
        return (new MailMessage)
            ->subject(__('notification.login_pin.mail.subject', $params))
            ->greeting(__('notification.login_pin.mail.greeting', $params))
            ->line(__('notification.login_pin.mail.line-1', $params))
            ->line(__('notification.login_pin.mail.line-2', $params))
            ->line(__('notification.login_pin.mail.line-3', $params))
            ->action(
                __('notification.login_pin.mail.cta', $params),
                route('login.pin.show'),
            )
            ->markdown('login-via-pin::notification', [
                'notifiable' => $notifiable,
                'pin' => str_split($this->pin),
            ]);
    }
}
