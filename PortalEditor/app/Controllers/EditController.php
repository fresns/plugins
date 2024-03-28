<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\PortalEditor\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\StrHelper;
use App\Models\Config;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function index(Request $request)
    {
        $configs = Config::whereIn('item_key', [
            'platforms',
            'portal_editor_auto',
            'default_language',
            'language_status',
            'language_menus',
        ])->get();

        $platforms = $configs->where('item_key', 'platforms')->first()?->item_value ?? [];
        $portalEditorAuto = $configs->where('item_key', 'portal_editor_auto')->first()?->item_value ?? true;
        $defaultLanguage = $configs->where('item_key', 'default_language')->first()?->item_value;
        $langStatus = $configs->where('item_key', 'language_status')->first()?->item_value ?? false;
        $langMenus = $configs->where('item_key', 'language_menus')->first()?->item_value ?? [];

        return view('PortalEditor::index', compact('platforms', 'portalEditorAuto', 'defaultLanguage', 'langStatus', 'langMenus'));
    }

    public function edit(int $id, string $langTag)
    {
        $configs = Config::whereIn('item_key', [
            'platforms',
            'language_menus',
            "portal_{$id}",
        ])->get();

        $platforms = $configs->where('item_key', 'platforms')->first()?->item_value ?? [];
        $langMenus = $configs->where('item_key', 'language_menus')->first()?->item_value ?? [];

        $key = array_search($id, array_column($platforms, 'id'));
        $langKey = array_search($langTag, array_column($langMenus, 'langTag'));

        $name = $platforms[$key]['name'] ?? null;
        $lang = $langMenus[$langKey] ?? null;

        $portalContent = $configs->where('item_key', "portal_{$id}")->first()?->item_value ?? [];

        $portal = StrHelper::languageContent($portalContent, $langTag);

        return view('PortalEditor::edit', compact('id', 'langTag', 'name', 'lang', 'portal'));
    }

    public function update(int $id, string $langTag, Request $request)
    {
        if (empty($request->content)) {
            return;
        }

        $itemKey = "portal_{$id}";
        $config = Config::withTrashed()->where('item_key', $itemKey)->first();

        if (! $config) {
            $config = new Config();

            $config->fill([
                'item_key' => $itemKey,
                'item_type' => 'object',
                'is_multilingual' => 1,
                'is_custom' => 1,
                'is_api' => 1,
            ]);
        }

        $itemValue = $config->item_value;
        $itemValue[$langTag] = $request->content;

        $config->item_value = $itemValue;
        $config->item_type = 'object';
        $config->is_multilingual = 1;
        $config->is_custom = 1;
        $config->is_api = 1;
        $config->deleted_at = null;
        $config->save();

        CacheHelper::forgetFresnsConfigs($itemKey);

        return $this->updateSuccess();
    }

    public function updateAuto()
    {
        $statusConfig = Config::where('item_key', 'portal_editor_auto')->firstOrFail();
        $statusConfig->item_value = ! $statusConfig->item_value;
        $statusConfig->save();

        CacheHelper::forgetFresnsConfigs('portal_editor_auto');

        return $this->updateSuccess();
    }

    public function updateNow()
    {
        $fresnsResp = \FresnsCmdWord::plugin('PortalEditor')->generateContent();

        if ($fresnsResp->isErrorResponse()) {
            return back()->with('failure', $fresnsResp->getMessage());
        }

        return $this->updateSuccess();
    }
}
