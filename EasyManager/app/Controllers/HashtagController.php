<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasyManager\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Models\Hashtag;
use Illuminate\Http\Request;

class HashtagController extends Controller
{
    public function index(Request $request)
    {
        $hashtagQuery = Hashtag::query();

        $hashtagQuery->when($request->id, function ($query, $value) {
            $query->where('id', $value);
        });

        $hashtagQuery->when($request->ids, function ($query, $value) {
            $idArr = json_decode($value, true);
            $query->whereIn('id', $idArr);
        });

        $hashtagQuery->when($request->hid, function ($query, $value) {
            $query->where('slug', $value);
        });

        $hashtagQuery->when($request->name, function ($query, $value) {
            $query->where('name', 'like', '%'.$value.'%');
        });

        $orderBy = $request->orderBy ?: 'created_at';
        $orderDirection = $request->orderDirection ?: 'desc';
        $hashtagQuery->orderBy($orderBy, $orderDirection);

        $hashtags = $hashtagQuery->paginate($request->get('pageSize', 15));

        // search config
        $search = [
            'status' => true,
            'action' => route('easy-manager.hashtag.index'),
            'selects' => [
                [
                    'name' => 'HID',
                    'value' => 'hid',
                ],
                [
                    'name' => __('EasyManager::fresns.table_name'),
                    'value' => 'name',
                ],
            ],
            'defaultSelect' => [
                'name' => __('EasyManager::fresns.table_name'),
                'value' => 'name',
            ],
        ];

        // site config
        $configKeys = ConfigHelper::fresnsConfigByItemKeys([
            'website_hashtag_detail_path',
            'site_url',
            'hashtag_liker_count',
            'hashtag_disliker_count',
            'hashtag_follower_count',
            'hashtag_blocker_count',
        ]);
        $url = $configKeys['site_url'].'/'.$configKeys['website_hashtag_detail_path'].'/';

        return view('EasyManager::hashtag', compact('hashtags', 'search', 'url'));
    }

    public function update(Hashtag $hashtag, Request $request)
    {
        $hashtag->is_enable = $request->is_enable;
        $hashtag->save();

        CacheHelper::clearDataCache('hashtag', $hashtag->slug, 'fresnsModel');
        CacheHelper::clearDataCache('hashtag', $hashtag->slug, 'fresnsApiData');

        return $this->updateSuccess();
    }
}
