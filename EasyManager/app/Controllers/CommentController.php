<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\EasyManager\Controllers;

use App\Helpers\ConfigHelper;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $commentQuery = Comment::with(['commentAppend', 'parentComment', 'author', 'post', 'hashtags', 'fileUsages']);

        $commentQuery->when($request->id, function ($query, $value) {
            $query->where('id', $value);
        });

        $commentQuery->when($request->cid, function ($query, $value) {
            $query->where('cid', $value);
        });

        $commentQuery->when($request->parentId, function ($query, $value) {
            $query->where('parent_id', $value);
        });

        $commentQuery->when($request->postId, function ($query, $value) {
            $query->where('post_id', $value);
        });

        $commentQuery->when($request->userId, function ($query, $value) {
            $query->where('user_id', $value);
        });

        $commentQuery->when($request->groupId, function ($query, $value) {
            $query->whereRelation('post', 'group_id', $value);
        });

        $commentQuery->when($request->hashtagId, function ($query, $value) {
            $query->whereHas('hashtags', function ($query) use ($value) {
                $query->where('hashtag_id', $value);
            });
        });

        $orderBy = $request->orderBy ?: 'created_at';
        $orderDirection = $request->orderDirection ?: 'desc';
        $commentQuery->orderBy($orderBy, $orderDirection);

        $comments = $commentQuery->paginate($request->get('pageSize', 15));

        // search config
        $search = [
            'status' => true,
            'action' => route('easy-manager.comment.index'),
            'selects' => [
                [
                    'name' => 'CID',
                    'value' => 'cid',
                ],
            ],
            'defaultSelect' => [
                'name' => 'CID',
                'value' => 'cid',
            ],
        ];

        // site config
        $configKeys = ConfigHelper::fresnsConfigByItemKeys([
            'website_comment_detail_path',
            'site_url',
            'comment_liker_count',
            'comment_disliker_count',
            'comment_follower_count',
            'comment_blocker_count',
        ]);
        $url = $configKeys['site_url'].'/'.$configKeys['website_comment_detail_path'].'/';

        return view('EasyManager::comment', compact('comments', 'search', 'url'));
    }

    public function destroy(Comment $comment, Request $request)
    {
        $comment->delete();

        return $this->deleteSuccess();
    }
}
