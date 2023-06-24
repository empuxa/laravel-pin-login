<?php

return [
    'handle_pin_request' => [
        'success' => 'Login erfolgreich.',
        'error' => [
            'rate_limit' => 'Zu viele falsche Anfragen. Ihre Account wurde für :seconds Sekunden geblockt.',
            'expired' => 'Der eingegebene PIN ist nicht mehr gültig. Wir haben Ihnen einen neuen PIN per E-Mail geschickt.',
            'wrong_pin' => 'Der PIN ist ungültig. Sie haben noch :attempts_left Versuche bis wir Ihren Account temporär blockieren.',
        ],
    ],
];
