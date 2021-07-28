<?php

namespace App\Http\Controllers;

use App\Models\Account;

class IndexController extends Controller
{
    /**
     * Render view.
     *
     * @param string $view
     * @param array $data
     * @return \Illuminate\Contracts\View\View
     */
    protected function render(string $view, array $data = [])
    {
        return view($view, $data);
    }

    /**
     * Check Is Key Exist.
     */
    protected function isAccountExist()
    {
        $sessionId = \Session::getId();
        $account = Account::where('session_id', $sessionId)->first();
        if (!$account) return false;

        return true;
    }

    /**
     * Check Is Key Exist.
     */
    protected function getSessionId()
    {
        return \Session::getId();
    }
}
