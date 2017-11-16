<?php
/**
 * Copyright (C) Marcelle Hövelmanns, art solution - All Rights Reserved
 *
 * @file        config.php
 * @author      Marcelle Hövelmanns
 * @site        http://www.artsolution.de
 * @date        26.10.17
 */

namespace AffiliconApiClient\Configurations;

return [

    /*
     * ---------------------------------------------------------------------
     * Environment Configuration
     * ---------------------------------------------------------------------
     */

    'environment' => [

        // Production
        'production' => [
            'service_url' => 'https://service.affilicon.net/api',
            'secure_url' => 'https://secure.affilibank.net'
        ],

        // Development
        'development' => [
            'service_url' => 'https://service.affilicon.app/api',
            'secure_url' => 'https://secure.affilibank.app'
        ],

        // Staging
        'staging' => [
            'service_url' => 'https://service-q.affilicon.net/api',
            'secure_url' => 'https://secure-q.affilibank.net'
        ]

    ],

    'error_log' => [
        'path' => __DIR__ . '/logs/error.log'
    ]

];

