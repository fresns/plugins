<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\HelperTool\Http\Controllers;

use App\Support\Helpers\ConfigHelper;
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
        $namespace = '\App\Support\Helpers\\';
        $helperClass = $request->get('helperClass');
        $helperName = $request->get('helperName');
        $params = $request->get('params');
        if (gettype($params) == 'string') {
            $params = json_decode($params, true);
        }
        $reflectionMethod = new \ReflectionMethod($namespace.$helperClass, $helperName);
        foreach ($reflectionMethod->getParameters() as $parameter) {
            $type = (string) $parameter->getType();
            $name = $parameter->getName();
            if (gettype($params[$name]) != $type) {
                settype($params[$name], $type);
            }
            $data[$name] = $params[$name] ?? '';
        }

        return $reflectionMethod->invoke(null, ...$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        return view('helper-tool::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('helper-tool::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('helper-tool::edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
