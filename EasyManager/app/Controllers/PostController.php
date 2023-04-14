<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasyManager\Controllers;

use App\Helpers\ConfigHelper;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $postQuery = Post::with(['postAppend', 'creator', 'group', 'hashtags', 'fileUsages']);

        $postQuery->when($request->id, function ($query, $value) {
            $query->where('id', $value);
        });

        $postQuery->when($request->pid, function ($query, $value) {
            $query->where('pid', $value);
        });

        $postQuery->when($request->userId, function ($query, $value) {
            $query->where('user_id', $value);
        });

        $postQuery->when($request->groupId, function ($query, $value) {
            $query->where('group_id', $value);
        });

        $postQuery->when($request->hashtagId, function ($query, $value) {
            $query->whereHas('hashtags', function ($query) use ($value) {
                $query->where('hashtag_id', $value);
            });
        });

        $orderBy = $request->orderBy ?: 'created_at';
        $orderDirection = $request->orderDirection ?: 'desc';
        $postQuery->orderBy($orderBy, $orderDirection);

        $posts = $postQuery->paginate($request->get('pageSize', 15));

        // search config
        $search = [
            'status' => true,
            'action' => route('easy-manager.post.index'),
            'selects' => [
                [
                    'name' => 'PID',
                    'value' => 'pid',
                ],
            ],
            'defaultSelect' => [
                'name' => 'PID',
                'value' => 'pid',
            ],
        ];

        // site config
        $configKeys = ConfigHelper::fresnsConfigByItemKeys([
            'website_post_detail_path',
            'site_url',
            'post_liker_count',
            'post_disliker_count',
            'post_follower_count',
            'post_blocker_count',
            'comment_liker_count',
            'comment_disliker_count',
            'comment_follower_count',
            'comment_blocker_count',
        ]);
        $url = $configKeys['site_url'].'/'.$configKeys['website_post_detail_path'].'/';

        return view('EasyManager::post', compact('posts', 'search', 'url'));
    }

    public function destroy(Post $post, Request $request)
    {
        $post->delete();

        return $this->deleteSuccess();
    }
}
