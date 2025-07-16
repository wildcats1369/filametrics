<?php

namespace wildcats1369\Filametrics\Helpers;

use Firebase\JWT\JWT;

class JwtTokenService
{
    protected string $saltKey;

    public function __construct()
    {
        // Use the same SALT key as in your Flask app
        $this->saltKey = env('PREDICT_API_KEY', 'your_default_salt_key');
    }

    /**
     * Generate JWT token for given payload
     *
     * @param array $payloadData Additional payload data (e.g. property_id, date)
     * @return string JWT token
     */
    public function generateToken(array $payloadData = []): string
    {
        // Generate JWT token (HS256 algorithm)
        return JWT::encode($payloadData, $this->saltKey, 'HS256');
    }
}
