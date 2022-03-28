<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\CmdWordTool\Controllers;

use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CmdWordToolController extends Controller
{
    use CmdWordResponseTrait;
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index(Request $request)
    {
        $unikey = $request->get('unikey') ?? 'Fresns';
        $wordName = $request->get('wordName');
        $param = $request->get('param');

        return \FresnsCmdWord::plugin($unikey)->$wordName($param);
    }
}
