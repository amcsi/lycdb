<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'amysql' => array (
        'host' => getenv('MYSQL_HOST') ?: 'localhost',
        'user' => 'lycdb',
        'password' => getenv('MYSQL_PASS') ?: '',
        'db'        => 'lycdb',
        'profile'   => false,
    ),
    'google_analytics' => array (
        'code' => getenv('GA_TRACKING_ID') ?: '',
        'domain' => 'lycee-tcg.eu',
    ),
    'viewconf' => array (
        'forums_url_base' => getenv('FORUMS_URL_BASE') ?: '//forums.lycee-tcg.eu',
        'forums_url_base_https' => getenv('FORUMS_URL_BASE_HTTPS') ?: 'https://forums.lycee-tcg.eu',
    )
);
