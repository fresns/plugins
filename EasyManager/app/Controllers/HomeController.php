<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasyManager\Controllers;

use App\Helpers\InteractionHelper;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $overview = InteractionHelper::fresnsOverview();

        // search config
        $search = [
            'status' => false,
            'action' => null,
            'selects' => [],
            'defaultSelect' => [],
        ];

        return view('EasyManager::home', compact('overview', 'search'));
    }
}
