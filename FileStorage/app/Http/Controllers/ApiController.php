<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FileStorage\Http\Controllers;

use App\Helpers\FileHelper as FresnsFileHelper;
use App\Helpers\PrimaryHelper;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Plugins\FileStorage\Helpers\ConfigHelper;
use Plugins\FileStorage\Helpers\FileHelper;

class ApiController extends Controller
{
    /**
     * get file.
     *
     * @param  string  $fid
     * @param  string  $token
     * @param  int  $time
     * @param  string  $type
     * @return file
     */
    // path: /api/file-storage/file?fid=c8N8g0Tf?type=config&token=94a08da1fecbb6e8b46990538c7b50b2&time=1676241447
    public function file(Request $request)
    {
        $fid = $request->fid;
        $token = $request->token;
        $time = (int) $request->time;
        $type = $request->type;

        if (empty($fid) || empty($token) || empty($time)) {
            return \response()->json([
                'code' => 30001,
            ]);
        }

        // check time
        if ($time < time()) {
            return \response()->json([
                'code' => 31303,
            ]);
        }

        // check file
        $file = PrimaryHelper::fresnsModelByFsid('file', $fid);
        if (empty($file)) {
            return \response()->json([
                'code' => 37500,
            ]);
        }

        // check token
        $checkToken = FileHelper::token($fid, $time, $type);
        if ($token != $checkToken) {
            return \response()->json([
                'code' => 31302,
            ]);
        }

        $filePath = $file->path;

        // image
        if ($file->type == File::TYPE_IMAGE) {
            if (empty($type)) {
                return \response()->json([
                    'code' => 30001,
                ]);
            }

            $imagePath = FresnsFileHelper::fresnsFilePathForImage('name-end', $file->path);

            $filePath = match ($type) {
                'config' => $imagePath['configPath'],
                'ratio' => $imagePath['ratioPath'],
                'square' => $imagePath['squarePath'],
                'big' => $imagePath['bigPath'],
                default => null,
            };
        }

        // video
        if ($file->type == File::TYPE_VIDEO && $type == 'poster') {
            $filePath = $file->video_poster_path;
        }

        // original
        if ($type == 'original') {
            $filePath = $file->original_path;
        }

        // check file path
        if (empty($filePath)) {
            return \response()->json([
                'code' => 32304,
            ]);
        }

        $diskConfig = ConfigHelper::disk($file->type);

        $fresnsStorage = Storage::build($diskConfig);

        return $fresnsStorage->download($filePath, $file->name);
    }
}
