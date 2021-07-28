<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckKeyRequest;
use App\Libraries\Utils;
use App\Models\Account;
use Http\Client\Exception\HttpException;
use Illuminate\Support\Facades\Http;

class SecurityController extends Controller
{
    /**
     * Check Key.
     *
     * @param CheckKeyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkApiKey(CheckKeyRequest $request)
    {
        $apiUrl  = Utils::MAILER_LITE_BASE_URL;
        $apiHeader  = Utils::getHttpHeader(['X-MailerLite-ApiKey' => $request->apiKey]);

        try {
            $response =  Http::withHeaders($apiHeader)->get($apiUrl);
            $rows = json_decode($response->body());

            if($rows) {
                if (isset($rows->error)) {
                    return response()->json($rows->error->message, $rows->error->code)->setCallback($request->input('callback'));
                }
                if (isset($rows->account)) {
                    $accountByApiKey = Account::where('key', $request->apiKey)->first();
                    $accountBySessionId = Account::where('session_id', $request->sessionId)->first();
                    if (!$accountBySessionId && !$accountByApiKey) {
                        $newAccount = new Account();
                        $newAccount->session_id = $request->sessionId;
                        $newAccount->key = $request->apiKey;
                        $newAccount->account_id = $rows->account->id;
                        $newAccount->email = $rows->account->email;
                        $newAccount->name = $rows->account->name;
                        $newAccount->subdomain = $rows->account->subdomain;
                        $newAccount->gmt = $rows->account->timezone->gmt;
                        $newAccount->save();
                    } elseif (!$accountBySessionId && $accountByApiKey) {
                        $accountByApiKey->session_id = $request->sessionId;
                        $accountByApiKey->save();
                    }
                }

                return response()->json(['success' => true], 200)->setCallback($request->input('callback'));
            }

        } catch (HttpException $e) {
            return response()->json($e->getMessage(), $e->getStatusCode())->setCallback($request->input('callback'));
        }
    }
}
