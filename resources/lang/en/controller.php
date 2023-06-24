<?php

return [
    'handle_pin_request' => [
        'success' => 'Successfully logged in.',
        'error'   => [
            'rate_limit' => 'Too many wrong requests. Your account is blocked for :seconds seconds.',
            'expired'    => "The PIN isn't valid any longer. We've sent you a new mail.",
            'wrong_pin'  => 'The PIN is wrong. You have :attempts_left more attempts until we temporarily block your account.',
        ],
    ],
];
