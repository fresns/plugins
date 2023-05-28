<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
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
        $fskey = $request->get('fskey') ?? 'Fresns';
        $wordName = $request->get('wordName');
        $param = $request->get('param');
        if ($request->file('param.file')) {
            $param['file'] = $request->file('param.file');
        }

        return \FresnsCmdWord::plugin($fskey)->$wordName($param);
    }
}
