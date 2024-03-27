<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasyManager\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Models\Comment;
use App\Models\Geotag;
use App\Models\Post;
use Illuminate\Http\Request;

class GeotagController extends Controller
{
    public function index(Request $request)
    {
        $geotagQuery = Geotag::query();

        $geotagQuery->when($request->id, function ($query, $value) {
            $query->where('id', $value);
        });

        $geotagQuery->when($request->ids, function ($query, $value) {
            $idArr = json_decode($value, true);
            $query->whereIn('id', $idArr);
        });

        $geotagQuery->when($request->type, function ($query, $value) {
            $query->where('type', $value);
        });

        $geotagQuery->when($request->gtid, function ($query, $value) {
            $query->where('gtid', $value);
        });

        $geotagQuery->when($request->name, function ($query, $value) {
            $query->where('name', 'like', '%'.$value.'%');
        });

        $orderBy = $request->orderBy ?: 'created_at';
        $orderDirection = $request->orderDirection ?: 'desc';
        $geotagQuery->orderBy($orderBy, $orderDirection);

        $geotags = $geotagQuery->paginate($request->get('pageSize', 15));

        // search config
        $search = [
            'status' => true,
            'action' => route('easy-manager.geotag.index'),
            'selects' => [
                [
                    'name' => 'GTID',
                    'value' => 'gtid',
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
            'website_geotag_detail_path',
            'geotag_liker_count',
            'geotag_disliker_count',
            'geotag_follower_count',
            'geotag_blocker_count',
        ]);

        $siteUrl = ConfigHelper::fresnsSiteUrl();

        $url = $siteUrl.'/'.$configKeys['website_geotag_detail_path'].'/';

        return view('EasyManager::geotag', compact('geotags', 'search', 'url'));
    }

    public function update(Geotag $geotag, Request $request)
    {
        if ($request->type) {
            $geotag->type = $request->type;
        }

        if ($request->has('is_enabled')) {
            $geotag->is_enabled = $request->is_enabled;
        }

        $geotag->save();

        CacheHelper::clearDataCache('geotag', $geotag->gtid);

        return $this->updateSuccess();
    }

    public function updateCount()
    {
        $geotags = Geotag::get();

        foreach ($geotags as $geotag) {
            $id = $geotag->id;

            $postCount = Post::where('geotag_id', $id)->count();

            $postDigestCount = Post::where('geotag_id', $id)->where('digest_state', '!=', '1')->count();

            $commentCount = Comment::where('geotag_id', $id)->count();

            $commentDigestCount = Comment::where('geotag_id', $id)->where('digest_state', '!=', '1')->count();

            $geotag->update([
                'post_count' => $postCount,
                'post_digest_count' => $postDigestCount,
                'comment_count' => $commentCount,
                'comment_digest_count' => $commentDigestCount,
            ]);
        }

        return $this->updateSuccess();
    }
}
