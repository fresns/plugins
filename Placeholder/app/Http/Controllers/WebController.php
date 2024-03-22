<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\Placeholder\Http\Controllers;

use Illuminate\Http\Request;

class WebController
{
    public function index(Request $request)
    {
        $params = $request->input();

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->verifyAccessToken([
            'accessToken' => $request->accessToken,
        ]);

        $headers = [];
        if ($fresnsResp->isSuccessResponse()) {
            $headers = $fresnsResp->getData();
        }

        return view('Placeholder::index', compact('params', 'headers'));
    }
}
