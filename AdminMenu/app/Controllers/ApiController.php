<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\AdminMenu\Controllers;

use App\Fresns\Api\Http\Controllers\GroupController;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ApiController extends Controller
{
    public function groupList(Request $request)
    {
        $service = new GroupController();

        return $service->list($request);
    }
}
