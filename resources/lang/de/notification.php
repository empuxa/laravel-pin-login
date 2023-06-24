<?php

return [
    'mail' => [
        'subject'  => 'Your login PIN for :app',
        'greeting' => 'Hello :name,',
        'line-1'   => 'There was a login request from :ip. Here is your PIN, which is valid until :valid_until:',
        'line-2'   => "If it wasn't you: no reason to panic. You can find more information about our login process here: https://google.com",
        'line-3'   => "Thank you for using our services. If you have any questions, please contact us. We're happy to help!",
        'cta'      => 'Sign in now',
    ],
];
