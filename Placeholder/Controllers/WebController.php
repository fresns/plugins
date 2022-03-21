<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\Placeholder\Controllers;

use Illuminate\Http\Request;

class WebController
{
    /**
     * This function is used to display the index page of the plugin
     * 
     * @param Request request The request object.
     * 
     * @return The view is being returned.
     */
    public function index(Request $request){
        $params = $request->input();
        return view('FsView::index', compact('params'));
    }
}
