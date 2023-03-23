<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\UtilityTool\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UtilityToolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index(Request $request)
    {
        $namespace = '\App\Utilities\\';
        $utilityClass = $request->get('utilityClass');
        $utilityName = $request->get('utilityName');
        $param = $request->get('param', []);
        $class = new ($namespace.$utilityClass);
        $reflectionMethod = (new \ReflectionMethod($class, $utilityName))->getParameters();
        foreach ($reflectionMethod as $key => $value) {
            if ($value->getType() == 'array') {
                $param[$key] = json_decode($param[$key], true);
            }
        }

        return $class->$utilityName(...$param);
    }
}
