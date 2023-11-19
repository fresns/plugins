<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasyManager\Controllers;

use Illuminate\Http\Request;

class CmdWordController extends Controller
{
    public function index(Request $request)
    {
        // search config
        $search = [
            'status' => false,
            'action' => null,
            'selects' => [],
            'defaultSelect' => [],
        ];

        $allCmdWords = \FresnsCmdWord::all();

        $cmdWords = [];
        foreach ($allCmdWords as $wordGroup) {
            $cmdWords = array_merge($cmdWords, $wordGroup);
        }

        return view('EasyManager::cmd-word', compact('search', 'cmdWords'));
    }
}
