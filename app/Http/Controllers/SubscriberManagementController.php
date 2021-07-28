<?php

namespace App\Http\Controllers;

use App\Models\Account;

class SubscriberManagementController extends IndexController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        if (!$this->isAccountExist()) return redirect()->route('checkKey');

        $sessionId = $this->getSessionId();
        $timeOffset = Account::select('gmt')->where('session_id', $sessionId)->first();
        $data['sessionId'] = $sessionId;
        $data['timeOffset'] = isset($timeOffset->gmt) ? $timeOffset->gmt : '+00:00';

        return $this->render('subscribers.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        if (!$this->isAccountExist()) return redirect()->route('checkKey');

        $data['sessionId'] = $this->getSessionId();

        return $this->render('subscribers.form', $data);
    }
}
