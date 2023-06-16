<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\NearbyDaysLimit\Services;

use App\Fresns\Api\Http\DTO\NearbyDTO;
use App\Fresns\Api\Services\CommentService;
use App\Fresns\Api\Services\PostService;
use App\Fresns\Api\Traits\ApiHeaderTrait;
use App\Helpers\ConfigHelper;
use App\Models\Comment;
use App\Models\Post;
use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CmdWordService
{
    use ApiHeaderTrait;
    use CmdWordResponseTrait;

    // getPostByNearby
    public function getPostByNearby($wordBody)
    {
        $dtoWordBody = new NearbyDTO($wordBody['body']);

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();

        $nearbyConfig = ConfigHelper::fresnsConfigByItemKeys([
            'nearby_length_km',
            'nearby_length_mi',
        ]);

        $unit = $dtoWordBody->unit ?? ConfigHelper::fresnsConfigLengthUnit($langTag);
        $length = $dtoWordBody->length ?? $nearbyConfig["nearby_length_{$unit}"];

        $nearbyLength = match ($unit) {
            'km' => $length,
            'mi' => $length * 0.6214,
            default => $length,
        };

        $nearbyDaysLimit = ConfigHelper::fresnsConfigByItemKey('nearby_days_limit') ?? 7;
        $daysLimit = now()->subDays($nearbyDaysLimit)->format('Y-m-d');

        $posts = Post::query()
            ->select(DB::raw("*, ( 6371 * acos( cos( radians($dtoWordBody->mapLat) ) * cos( radians( map_latitude ) ) * cos( radians( map_longitude ) - radians($dtoWordBody->mapLng) ) + sin( radians($dtoWordBody->mapLat) ) * sin( radians( map_latitude ) ) ) ) AS distance"))
            ->having('distance', '<=', $nearbyLength)
            ->whereDate('created_at', '>=', $daysLimit)
            ->orderBy('distance')
            ->paginate($dtoWordBody->pageSize ?? 15);

        $postList = [];
        $service = new PostService();
        foreach ($posts as $post) {
            $postList[] = $service->postData($post, 'list', $langTag, $timezone, $authUser?->id, $dtoWordBody->mapId, $dtoWordBody->mapLng, $dtoWordBody->mapLat, true);
        }

        $paginate = new LengthAwarePaginator(
            items: $postList,
            total: $posts->total(),
            perPage: $posts->perPage(),
            currentPage: \request('page'),
        );

        $paginate->withPath('/'.\request()->path())->withQueryString();

        return $this->success([
            'paginate' => [
                'total' => $paginate->total(),
                'pageSize' => $paginate->perPage(),
                'currentPage' => $paginate->currentPage(),
                'lastPage' => $paginate->lastPage(),
            ],
            'list' => array_map(function ($item) {
                return $item;
            }, $paginate->items()),
        ]);
    }

    // getCommentByNearby
    public function getCommentByNearby($wordBody)
    {
        $dtoWordBody = new NearbyDTO($wordBody['body']);

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();

        $nearbyConfig = ConfigHelper::fresnsConfigByItemKeys([
            'nearby_length_km',
            'nearby_length_mi',
        ]);

        $unit = $dtoWordBody->unit ?? ConfigHelper::fresnsConfigLengthUnit($langTag);
        $length = $dtoWordBody->length ?? $nearbyConfig["nearby_length_{$unit}"];

        $nearbyLength = match ($unit) {
            'km' => $length,
            'mi' => $length * 0.6214,
            default => $length,
        };

        $nearbyDaysLimit = ConfigHelper::fresnsConfigByItemKey('nearby_days_limit') ?? 7;
        $daysLimit = now()->subDays($nearbyDaysLimit)->format('Y-m-d');

        $comments = Comment::query()
            ->select(DB::raw("*, ( 6371 * acos( cos( radians($dtoWordBody->mapLat) ) * cos( radians( map_latitude ) ) * cos( radians( map_longitude ) - radians($dtoWordBody->mapLng) ) + sin( radians($dtoWordBody->mapLat) ) * sin( radians( map_latitude ) ) ) ) AS distance"))
            ->having('distance', '<=', $nearbyLength)
            ->whereDate('created_at', '>=', $daysLimit)
            ->orderBy('distance')
            ->paginate($dtoWordBody->pageSize ?? 15);

        $commentConfig = [
            'userId' => $authUser?->id,
            'mapId' => $dtoWordBody->mapId,
            'longitude' => $dtoWordBody->mapLng,
            'latitude' => $dtoWordBody->mapLat,
            'outputSubComments' => true,
            'outputReplyToPost' => true,
            'outputReplyToComment' => true,
            'whetherToFilter' => true,
        ];

        $commentList = [];
        $service = new CommentService();
        foreach ($comments as $comment) {
            $commentList[] = $service->commentData(
                $comment,
                'list',
                $langTag,
                $timezone,
                $commentConfig['userId'],
                $commentConfig['mapId'],
                $commentConfig['longitude'],
                $commentConfig['latitude'],
                $commentConfig['outputSubComments'],
                $commentConfig['outputReplyToPost'],
                $commentConfig['outputReplyToComment'],
                $commentConfig['whetherToFilter'],
            );
        }

        $paginate = new LengthAwarePaginator(
            items: $commentList,
            total: $comments->total(),
            perPage: $comments->perPage(),
            currentPage: \request('page'),
        );

        $paginate->withPath('/'.\request()->path())->withQueryString();

        return $this->success([
            'paginate' => [
                'total' => $paginate->total(),
                'pageSize' => $paginate->perPage(),
                'currentPage' => $paginate->currentPage(),
                'lastPage' => $paginate->lastPage(),
            ],
            'list' => array_map(function ($item) {
                return $item;
            }, $paginate->items()),
        ]);
    }
}
