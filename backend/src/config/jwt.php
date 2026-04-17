<?php
/**
 * JWT Configuration
 */

return [
    'secret' => getenv('JWT_SECRET') ?: 'your-secret-key-change-this',
    'expiry' => getenv('JWT_EXPIRY') ?: 604800, // 7 days in seconds
    'algorithm' => 'HS256',
];
?>
