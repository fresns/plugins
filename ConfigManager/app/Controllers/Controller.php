<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\ConfigManager\Controllers;

use App\Helpers\PluginHelper;
use App\Models\Config;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    protected $locale;

    protected $defaultLanguage;

    protected $optionalLanguages;

    public function __construct()
    {
        $version = PluginHelper::fresnsPluginVersionByFskey('ConfigManager');
        View::share('version', $version);

        $locale = \request()->cookie('fresns_panel_lang');
        $this->locale = $locale;
        config(['app.locale' => $locale]);
        View::share('locale', $locale);

        // default language
        $defaultLanguageConfig = Config::where('item_key', 'default_language')->first();

        $defaultLanguage = $defaultLanguageConfig ? $defaultLanguageConfig->item_value : config('app.locale');
        $this->defaultLanguage = $defaultLanguage;
        View::share('defaultLanguage', $defaultLanguage);

        // Available languages
        $status = Config::where('item_key', 'language_status')->first();

        $languageConfig = Config::where('item_key', 'language_menus')->first();
        $optionalLanguages = $languageConfig ? $languageConfig->item_value : [];
        if (! $status || ! $status->item_value) {
            $optionalLanguages = collect($optionalLanguages)->where('langTag', $defaultLanguage)->all();
        }
        $this->optionalLanguages = $optionalLanguages;
        View::share('optionalLanguages', collect($optionalLanguages));
    }

    public function requestSuccess()
    {
        return $this->successResponse('request');
    }

    public function createSuccess()
    {
        return $this->successResponse('create');
    }

    public function updateSuccess()
    {
        return $this->successResponse('update');
    }

    public function deleteSuccess()
    {
        return $this->successResponse('delete');
    }

    public function successResponse($action)
    {
        return request()->ajax()
            ? response()->json(['message' => __('FsLang::tips.'.$action.'Success')], 200)
            : back()->with('success', __('FsLang::tips.'.$action.'Success'));
    }
}
