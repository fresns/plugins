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
use App\Models\Hashtag;
use App\Models\Post;
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

        $hashtagQuery->when($request->type, function ($query, $value) {
            $query->where('type', $value);
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
        if ($request->type) {
            $hashtag->type = $request->type;
        }

        if ($request->has('is_enabled')) {
            $hashtag->is_enabled = $request->is_enabled;
        }

        $hashtag->save();

        CacheHelper::clearDataCache('hashtag', $hashtag->slug, 'fresnsModel');
        CacheHelper::clearDataCache('hashtag', $hashtag->slug, 'fresnsApiData');

        return $this->updateSuccess();
    }

    public function updateCount()
    {
        $hashtags = Hashtag::get();

        foreach ($hashtags as $hashtag) {
            $id = $hashtag->id;

            $postCount = Comment::with(['hashtags'])->whereHas('hashtags', function ($query) use ($id) {
                $query->where('hashtag_id', $id);
            })->count();

            $postDigestCount = Post::with(['hashtags'])->where('digest_state', '!=', '1')->whereHas('hashtags', function ($query) use ($id) {
                $query->where('hashtag_id', $id);
            })->count();

            $commentCount = Comment::with(['hashtags'])->whereHas('hashtags', function ($query) use ($id) {
                $query->where('hashtag_id', $id);
            })->count();

            $commentDigestCount = Comment::with(['hashtags'])->where('digest_state', '!=', '1')->whereHas('hashtags', function ($query) use ($id) {
                $query->where('hashtag_id', $id);
            })->count();

            $hashtag->update([
                'post_count' => $postCount,
                'post_digest_count' => $postDigestCount,
                'comment_count' => $commentCount,
                'comment_digest_count' => $commentDigestCount,
            ]);
        }

        return $this->updateSuccess();
    }
}
