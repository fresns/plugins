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
        $param = $request->get('param');
        if (gettype($param) == 'string') {
            $param = json_decode($param, true);
        }
        $reflectionMethod = new \ReflectionMethod($namespace . $helperClass, $helperName);
        foreach ($reflectionMethod->getParameters() as $parameter) {
            $type = (string)$parameter->getType();
            $name = $parameter->getName();
            if (isset($param[$name])) {
                if (gettype($param[$name]) != $type && !$parameter->isDefaultValueAvailable()) {
                    settype($param[$name], $type);
                }
                $data[$name] = $param[$name] ?? '';
            }
        }

        return $reflectionMethod->invoke(null, ...$data);
    }
}
