<?php

namespace App\Http\Controllers;

class CheckKeyController extends IndexController
{
    /**
     * Display Check Key input.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function check()
    {
        if ($this->isAccountExist()) return redirect()->route('subscribers.index');

        $data['sessionId'] = $this->getSessionId();

        return $this->render('start.index', $data);
    }
}
