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
use App\Models\Language;
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
        $operation->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $operation->description = $request->descriptions[$this->defaultLanguage] ?? (current(array_filter($request->descriptions)) ?: '');
        $operation->image_file_url = $request->image_file_url;
        $operation->is_enabled = $request->is_enabled;
        $operation->plugin_fskey = 'TitleIcons';
        $operation->save();

        if ($request->file('image_file')) {
            $wordBody = [
                'usageType' => FileUsage::TYPE_OPERATION,
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
            $fileId = PrimaryHelper::fresnsFileIdByFid($fresnsResp->getData('fid'));

            $operation->image_file_id = $fileId;
            $operation->image_file_url = null;
            $operation->save();
        }

        if ($request->update_name) {
            foreach ($request->names as $langTag => $content) {
                $language = Language::tableName('operations')
                    ->where('table_id', $operation->id)
                    ->where('table_column', 'name')
                    ->where('lang_tag', $langTag)
                    ->first();

                if (! $language) {
                    // create but no content
                    if (! $content) {
                        continue;
                    }
                    $language = new Language();
                    $language->fill([
                        'table_name' => 'operations',
                        'table_column' => 'name',
                        'table_id' => $operation->id,
                        'lang_tag' => $langTag,
                    ]);
                }

                $language->lang_content = $content;
                $language->save();
            }
        }

        if ($request->update_description) {
            foreach ($request->descriptions as $langTag => $content) {
                $language = Language::tableName('operations')
                    ->where('table_id', $operation->id)
                    ->where('table_column', 'description')
                    ->where('lang_tag', $langTag)
                    ->first();

                if (! $language) {
                    // create but no content
                    if (! $content) {
                        continue;
                    }
                    $language = new Language();
                    $language->fill([
                        'table_name' => 'operations',
                        'table_column' => 'description',
                        'table_id' => $operation->id,
                        'lang_tag' => $langTag,
                    ]);
                }

                $language->lang_content = $content;
                $language->save();
            }
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
        $operation->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $operation->description = $request->descriptions[$this->defaultLanguage] ?? (current(array_filter($request->descriptions)) ?: '');
        $operation->image_file_url = $request->image_file_url;
        $operation->is_enabled = $request->is_enabled;
        $operation->plugin_fskey = 'TitleIcons';
        $operation->save();

        if ($request->file('image_file')) {
            $wordBody = [
                'usageType' => FileUsage::TYPE_OPERATION,
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
            $fileId = PrimaryHelper::fresnsFileIdByFid($fresnsResp->getData('fid'));

            $operation->image_file_id = $fileId;
            $operation->image_file_url = null;
            $operation->save();
        }

        if ($request->update_name) {
            foreach ($request->names as $langTag => $content) {
                $language = Language::tableName('operations')
                    ->where('table_id', $operation->id)
                    ->where('table_column', 'name')
                    ->where('lang_tag', $langTag)
                    ->first();

                if (! $language) {
                    // create but no content
                    if (! $content) {
                        continue;
                    }
                    $language = new Language();
                    $language->fill([
                        'table_name' => 'operations',
                        'table_column' => 'name',
                        'table_id' => $operation->id,
                        'lang_tag' => $langTag,
                    ]);
                }

                $language->lang_content = $content;
                $language->save();
            }
        }

        if ($request->update_description) {
            foreach ($request->descriptions as $langTag => $content) {
                $language = Language::tableName('operations')
                    ->where('table_id', $operation->id)
                    ->where('table_column', 'description')
                    ->where('lang_tag', $langTag)
                    ->first();

                if (! $language) {
                    // create but no content
                    if (! $content) {
                        continue;
                    }
                    $language = new Language();
                    $language->fill([
                        'table_name' => 'operations',
                        'table_column' => 'description',
                        'table_id' => $operation->id,
                        'lang_tag' => $langTag,
                    ]);
                }

                $language->lang_content = $content;
                $language->save();
            }
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
