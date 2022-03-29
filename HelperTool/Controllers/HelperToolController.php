<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\HelperTool\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HelperToolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index(Request $request)
    {
        $namespace = '\App\Helpers\\';
        $helperClass = $request->get('helperClass');
        $helperName = $request->get('helperName');
        $param = $request->get('param', []);
        $class = new ($namespace.$helperClass);

        return $class->$helperName(...$param);
    }
}
