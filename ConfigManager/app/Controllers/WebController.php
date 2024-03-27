<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\ConfigManager\Controllers;

use App\Models\Config;
use App\Utilities\ConfigUtility;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function index(Request $request)
    {
        $configQuery = Config::query();

        $configQuery->when($request->key, function ($query, $value) {
            $query->where('item_key', 'like', "%$value%");
        });

        $configQuery->when($request->type, function ($query, $value) {
            $query->where('item_type', $value);
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

    public function update(Request $request)
    {
        $request->validate([
            'item_key' => ['required', 'string'],
            'item_type' => ['required', 'string'],
            'item_value' => ['nullable'],
            'langValues' => ['nullable'],
            'is_multilingual' => ['required', 'boolean'],
            'is_api' => ['required', 'boolean'],
        ]);

        $id = $request->id;
        $item_key = $request->item_key;
        $item_type = $request->item_type;
        $is_multilingual = $request->is_multilingual;
        $is_api = $request->is_api;

        $item_value = $is_multilingual ? $request->langValues : $request->item_value;

        $fresnsConfigItems = [
            [
                'item_key' => $item_key,
                'item_value' => $item_value,
                'item_type' => $is_multilingual ? 'object' : $item_type, // number, string, boolean, array, object, file, plugin, plugins
                'is_multilingual' => $is_multilingual,
                'is_api' => $is_api,
            ],
        ];

        if ($id) {
            ConfigUtility::changeFresnsConfigItems($fresnsConfigItems);

            return $this->updateSuccess();
        }

        ConfigUtility::addFresnsConfigItems($fresnsConfigItems);

        return $this->createSuccess();
    }

    public function delete(Request $request)
    {
        $request->validate([
            'item_key' => ['required', 'string'],
        ]);

        $itemKey = $request->item_key;

        $fresnsConfigKeys = [
            $itemKey,
        ];

        ConfigUtility::removeFresnsConfigItems($fresnsConfigKeys);

        return $this->deleteSuccess();
    }
}
