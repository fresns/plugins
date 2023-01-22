<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasyManager\Controllers;

use App\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $fileQuery = File::with(['fileUsages']);

        $fileQuery->when($request->id, function ($query, $value) {
            $query->where('id', $value);
        });

        $fileQuery->when($request->ids, function ($query, $value) {
            $idArr = json_decode($value, true);
            $query->whereIn('id', $idArr);
        });

        $fileQuery->when($request->fid, function ($query, $value) {
            $query->where('fid', $value);
        });

        $fileQuery->when($request->type, function ($query, $value) {
            $query->where('type', $value);
        });

        $fileQuery->when($request->orderBy, function ($query, $value) {
            $query->$value();
        });

        if (empty($request->orderBy)) {
            $fileQuery->latest();
        }

        $files = $fileQuery->paginate($request->get('pageSize', 15));

        // search config
        $search = [
            'status' => true,
            'action' => route('easy-manager.file.index'),
            'selects' => [
                [
                    'name' => 'FID',
                    'value' => 'fid',
                ],
            ],
            'defaultSelect' => [
                'name' => 'FID',
                'value' => 'fid',
            ],
        ];

        return view('EasyManager::file', compact('files', 'search'));
    }

    public function destroy(File $file, Request $request)
    {
        $file->delete();

        return $this->deleteSuccess();
    }
}
