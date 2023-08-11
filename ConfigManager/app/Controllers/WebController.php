<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\ConfigManager\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use App\Utilities\ConfigUtility;

class WebController extends Controller
{
    public function index(Request $request)
    {
        $configQuery = Config::query()->with('languages');

        $configQuery->when($request->key, function ($query, $value) {
            $query->where('item_key', 'like', "%$value%");
        });

        $configQuery->when($request->type, function ($query, $value) {
            $query->where('item_type', $value);
        });

        $configQuery->when($request->tag, function ($query, $value) {
            $query->where('item_tag', 'like', "%$value%");
        });

        if (isset($request->multilingual)) {
            $configQuery->where('is_multilingual', $request->multilingual);
        }

        if (isset($request->custom)) {
            $configQuery->where('is_custom', $request->custom);
        }

        if (isset($request->api)) {
            $configQuery->where('is_api', $request->api);
        }

        $orderBy = $request->orderBy ?: 'id';
        $orderDirection = $request->orderDirection ?: 'asc';
        $configQuery->orderBy($orderBy, $orderDirection);

        $configs = $configQuery->paginate($request->get('pageSize', 15));

        return view('ConfigManager::index', compact('configs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string'],
            'value' => ['nullable'],
            'type' => ['required', 'string'],
            'tag' => ['required', 'string'],
            'multilingual' => ['required', 'boolean'],
            'api' => ['required', 'boolean'],
        ]);

        $data = [];
        $data['id'] = $request->input('id');
        $data['item_key'] = $request->string('key');
        $data['item_value'] = $request->string('value');
        $data['item_type'] = $request->string('type');
        $data['item_tag'] = $request->string('tag');
        $data['is_multilingual'] = $request->boolean('multilingual');
        $data['is_api'] = $request->boolean('api');

        $languageValues = [];
        if ($request->update_value) {
            // $languageValues = [
            //     'en' => 'English Content',
            //     'zh-Hans' => '中文内容', // 多语言内容
            // ];
            $data['item_value'] = '';
            $languageValues = $request->get('values');
        }

        $fresnsConfigItems = [
            [
                'item_key' => $data['item_key'],
                'item_value' => $data['item_value'],
                'item_type' => $data['item_type'], // number, string, boolean, array, object, file, plugin, plugins
                'item_tag' => $data['item_tag'],
                'is_multilingual' => $data['is_multilingual'],
                'is_api' => $data['is_api'],
                'language_values' => $languageValues,
            ],
        ];

        switch ($request->method()) {
            case 'POST':
                ConfigUtility::addFresnsConfigItems($fresnsConfigItems);
                break;
            case 'PUT':
                ConfigUtility::changeFresnsConfigItems($fresnsConfigItems);
                break;
        }

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => null,
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'item_key' => ['required', 'string'],
        ]);

        $itemKey = $request->input('item_key');

        $fresnsConfigKeys = [
            $itemKey,
        ];

        ConfigUtility::removeFresnsConfigItems($fresnsConfigKeys);

        return $this->deleteSuccess();
    }
}
