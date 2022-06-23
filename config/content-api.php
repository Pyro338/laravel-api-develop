<?php

/**
 * @file
 * This contains settings related to content API.
 */

return [
    'base_uri' => env('CONTENT_API_BASE_URI', 'https://devpub-content.dbd.net'),
    'bearer_token' => env('CONTENT_API_BEARER_TOKEN', null)
];
