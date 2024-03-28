<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\TitleIcons\Controllers;

use App\Helpers\PrimaryHelper;
use App\Models\File;
use App\Models\FileUsage;
use App\Models\Operation;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // index
    public function index(Request $request)
    {
        $operations = Operation::where('type', Operation::TYPE_DIVERSIFY_IMAGE)->where('code', 'title')->paginate($request->get('pageSize', 15));

        return view('TitleIcons::admin.list', compact('operations'));
    }

    // store
    public function store(Operation $operation, Request $request)
    {
        $operation->type = Operation::TYPE_DIVERSIFY_IMAGE;
        $operation->code = 'title';
        $operation->style = 'primary';
        $operation->name = $request->names;
        $operation->description = $request->descriptions;
        $operation->image_file_url = $request->image_file_url;
        $operation->is_enabled = $request->is_enabled;
        $operation->app_fskey = 'TitleIcons';
        $operation->save();

        if ($request->file('image_file')) {
            $wordBody = [
                'usageType' => FileUsage::TYPE_SYSTEM,
                'platformId' => 4,
                'tableName' => 'operations',
                'tableColumn' => 'image_file_id',
                'tableId' => $operation->id,
                'type' => File::TYPE_IMAGE,
                'file' => $request->file('image_file'),
            ];
            $fresnsResp = \FresnsCmdWord::plugin('Fresns')->uploadFile($wordBody);
            if ($fresnsResp->isErrorResponse()) {
                return back()->with('failure', $fresnsResp->getMessage());
            }
            $fileId = PrimaryHelper::fresnsPrimaryId('file', $fresnsResp->getData('fid'));

            $operation->image_file_id = $fileId;
            $operation->image_file_url = null;
            $operation->save();
        }

        return $this->createSuccess();
    }

    // update
    public function update(int $id, Request $request)
    {
        $operation = Operation::findOrFail($id);
        $operation->type = Operation::TYPE_DIVERSIFY_IMAGE;
        $operation->code = 'title';
        $operation->style = 'primary';
        $operation->name = $request->names;
        $operation->description = $request->descriptions;
        $operation->image_file_url = $request->image_file_url;
        $operation->is_enabled = $request->is_enabled;
        $operation->app_fskey = 'TitleIcons';

        $operation->save();

        if ($request->file('image_file')) {
            $wordBody = [
                'usageType' => FileUsage::TYPE_SYSTEM,
                'platformId' => 4,
                'tableName' => 'operations',
                'tableColumn' => 'image_file_id',
                'tableId' => $operation->id,
                'type' => File::TYPE_IMAGE,
                'file' => $request->file('image_file'),
            ];
            $fresnsResp = \FresnsCmdWord::plugin('Fresns')->uploadFile($wordBody);
            if ($fresnsResp->isErrorResponse()) {
                return back()->with('failure', $fresnsResp->getMessage());
            }
            $fileId = PrimaryHelper::fresnsPrimaryId('file', $fresnsResp->getData('fid'));

            $operation->image_file_id = $fileId;
            $operation->image_file_url = null;
            $operation->save();
        }

        return $this->updateSuccess();
    }

    // destroy
    public function destroy(int $id)
    {
        Operation::where('id', $id)->delete();

        return $this->deleteSuccess();
    }
}
