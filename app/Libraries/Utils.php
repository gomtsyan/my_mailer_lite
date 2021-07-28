<?php

namespace App\Libraries;

use App\Models\Account;

class Utils
{
    const MAILER_LITE_BASE_URL = 'https://api.mailerlite.com/api/v2';

    /**
     * Get http header.
     * @param array $header
     * @return array
     */
    public static function getHttpHeader(array $header = []){
        $defaultHeader = [
            'Content-Type' => 'application/json',
            'X-MailerLite-ApiKey' => config('app.POSTMAN_API.POSTMAN_API_KEY'),
        ];
        if (!empty($header)) {
            return array_merge($defaultHeader, $header);
        }

        return $defaultHeader;
    }

    /**
     * Get http header.
     * @param string $sessionId
     * @return string
     */
    public static function getApiKey(string $sessionId){
        $account = Account::select('key')->where('session_id', $sessionId)->first();

        return $account->key ?? '';
    }
}
