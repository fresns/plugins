<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\CmdWordTool\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;

class CmdWordToolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index(Request $request)
    {
        $unikey = $request->get('unikey') ?? 'Fresns';
        $wordName = $request->get('wordName');
        $params = $request->get('params');
        if (gettype($params) == 'string') {
            $params = json_decode($params, true);
        }
        $commandList = \FresnsCmdWord::all();
        $controller = Arr::get($commandList, $unikey.'.'.$wordName.'.provider.0');
        if (empty($controller)) {
            return ['code' => 500, 'message' => 'empty wordName'];
        }
        $controller = new $controller;
        $data = [];
        foreach ((new \ReflectionMethod($controller, $wordName))->getParameters() as $parameter) {
            $className = $parameter->getClass();
            $name = $parameter->getName();
            if (gettype($params[$name]) == 'string' && $this->isJson($params[$name])) {
                $params[$name] = json_decode($params[$name], true);
            }
            if (empty($className)) {
                $data[$name] = $params[$name];
            } else {
                $class = $parameter->getClass();
                $class = new $class->name($params[$name]);
                $data[$name] = $class;
            }
        }
        if ($request->hasFile('file')) {
            $data['wordBody']['file'] = $request->file('file');
        }

        return \FresnsCmdWord::plugin($unikey)->$wordName(...$data);
    }

    public function isJson($string)
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        return view('cmd-word-tool::create');
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
        return view('cmd-word-tool::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('cmd-word-tool::edit');
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
