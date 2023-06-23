<?php

return [
    /**
     * The model to use for the login.
     * Default: App\Models\User::class
     */
    'model' => \App\Models\User::class,

    'columns' => [
        /**
         * The column where the PIN is stored.
         * Default: 'login_pin'
         */
        'pin' => 'login_pin',

        /**
         * The column where we store the information, how long the PIN is valid.
         * Default: 'login_pin_valid_until'
         */
        'pin_valid_until' => 'login_pin_valid_until',
    ],

    'pin' => [
        /**
         * The time in seconds after which the PIN expires.
         * This is the information being stored in the `login_pin_valid_until` column.
         * Default: 600
         */
        'expires_in' => 600,

        /**
         * The maximum number of attempts to enter a PIN per minute.
         * Afterward, the user gets blocked for 60 seconds.
         * See the default Laravel RateLimiter for more information.
         * Default: 5
         */
        'max_attempts' => 5,

        /**
         * The length of the PIN.
         * Keep in mind that longer PINs might break the layout.
         * Default: 6
         */
        'length' => 6,

        /**
         * The validation rules for the PIN.
         * Default: 'required|digits:6'
         */
        'validation' => 'required|digits:6',
    ],

    'email' => [
        /**
         * The maximum number of attempts to get the user per minute.
         * Afterward, the user gets blocked for 60 seconds.
         * See the default Laravel RateLimiter for more information.
         * Default: 5
         */
        'max_attempts' => 5,

        /**
         * The validation rules for the email.
         * Default: 'email|required'
         */
        'validation' => 'email|required',
    ],

    /**
     * The notification to send to the user.
     * Default: \Empuxa\LoginViaPin\Notifications\LoginPin::class
     */
    'notification' => \Empuxa\LoginViaPin\Notifications\LoginPin::class,

    'route' => [
        /**
         * The middleware to use for the route.
         * Default: ['web']
         */
        'middleware' => ['web'],

        /**
         * The prefix for the route.
         * Default: 'login'
         */
        'prefix' => 'login',
    ],

    /**
     * The redirect path after a successful login.
     * Default: '/'
     */
    'redirect' => '/',
];
