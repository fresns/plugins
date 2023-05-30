<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\PortalEditor\Services;

use App\Fresns\Api\Http\Controllers\HashtagController;
use App\Fresns\Api\Http\Controllers\PostController;
use App\Fresns\Api\Http\Controllers\UserController;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Models\Config;
use App\Models\Language;
use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class CmdWordService
{
    use CmdWordResponseTrait;

    // generateContent
    public function generateContent()
    {
        $portalEditorAuto = Config::where('item_key', 'portal_editor_auto')->first()?->item_value ?? true;

        if (! $portalEditorAuto) {
            return $this->failure(21010);
        }

        info('Cmd Word', ['PortalEditor->generateContent']);

        $configKeys = ConfigHelper::fresnsConfigByItemKeys([
            'default_language',
            'language_status',
            'language_menus',
        ]);

        if (! $configKeys['language_status']) {
            $data = self::generate($configKeys['default_language']);
            $titleArr = self::titleArr($configKeys['default_language']);
            $urlArr = self::urlArr($configKeys['default_language']);

            $html = View::make('PortalEditor::template', compact('data', 'titleArr', 'urlArr'))->render();

            self::saveHtml($configKeys['default_language'], $html);

            CacheHelper::forgetFresnsConfigs('portal_4');

            return $this->success();
        }

        foreach ($configKeys['language_menus'] as $lang) {
            $data = self::generate($lang['langTag']);
            $titleArr = self::titleArr($lang['langTag']);
            $urlArr = self::urlArr($lang['langTag']);

            $html = View::make('PortalEditor::template', compact('data', 'titleArr', 'urlArr'))->render();

            self::saveHtml($lang['langTag'], $html);
        }

        CacheHelper::forgetFresnsConfigs('portal_4');

        return $this->success();
    }

    public static function generate(): array
    {
        $postApiController = new PostController();

        // sticky result
        $stickyRequest = Request::create('/api/v2/post/list', 'GET', [
            'stickyState' => 3,
        ]);
        $stickyResponse = $postApiController->list($stickyRequest);

        if (is_array($stickyResponse)) {
            $stickyResult = $stickyResponse;
        } else {
            $stickyResultContent = $stickyResponse->getContent();
            $stickyResult = json_decode($stickyResultContent, true);
        }

        // Query Config
        $queryConfig = [
            'user' => ConfigHelper::fresnsConfigByItemKey('menu_user_query_config'),
            'hashtag' => ConfigHelper::fresnsConfigByItemKey('menu_hashtag_query_config'),
            'post' => ConfigHelper::fresnsConfigByItemKey('menu_post_list_query_config'),
        ];

        // post list
        $postParams = [];
        if ($queryConfig['post']) {
            $urlInfo = parse_url($queryConfig['post']);

            if ($urlInfo['path']) {
                parse_str($urlInfo['path'], $postParams);
            }
        }

        $postRequest = Request::create('/api/v2/post/list', 'GET', $postParams);
        $postResponse = $postApiController->list($postRequest);

        if (is_array($postResponse)) {
            $postResult = $postResponse;
        } else {
            $postResultContent = $postResponse->getContent();
            $postResult = json_decode($postResultContent, true);
        }

        // users
        $userApiController = new UserController();

        $userParams = [];
        if ($queryConfig['user']) {
            $urlInfo = parse_url($queryConfig['user']);

            if ($urlInfo['path']) {
                parse_str($urlInfo['path'], $userParams);
            }
        }

        $userRequest = Request::create('/api/v2/user/list', 'GET', $userParams);
        $userResponse = $userApiController->list($userRequest);

        if (is_array($userResponse)) {
            $userResult = $userResponse;
        } else {
            $userResultContent = $userResponse->getContent();
            $userResult = json_decode($userResultContent, true);
        }

        // hashtags
        $hashtagApiController = new HashtagController();

        $hashtagParams = [];
        if ($queryConfig['hashtag']) {
            $urlInfo = parse_url($queryConfig['hashtag']);

            if ($urlInfo['path']) {
                parse_str($urlInfo['path'], $hashtagParams);
            }
        }

        $hashtagRequest = Request::create('/api/v2/hashtag/list', 'GET', $hashtagParams);
        $hashtagResponse = $hashtagApiController->list($hashtagRequest);

        if (is_array($hashtagResponse)) {
            $hashtagResult = $hashtagResponse;
        } else {
            $hashtagResultContent = $hashtagResponse->getContent();
            $hashtagResult = json_decode($hashtagResultContent, true);
        }

        return [
            'stickyPosts' => $stickyResult['data']['list'],
            'posts' => $postResult['data']['list'],
            'users' => $userResult['data']['list'],
            'hashtags' => $hashtagResult['data']['list'],
        ];
    }

    public static function titleArr(string $langTag): array
    {
        $fsLang = ConfigHelper::fresnsConfigApiByItemKey('language_pack_contents', $langTag);
        $menuHashtagName = ConfigHelper::fresnsConfigApiByItemKey('menu_hashtag_name', $langTag);
        $menuUserName = ConfigHelper::fresnsConfigApiByItemKey('menu_user_name', $langTag);
        $menuPostListName = ConfigHelper::fresnsConfigApiByItemKey('menu_post_list_name', $langTag);

        return [
            'sticky' => $fsLang['contentSticky'],
            'hashtag' => $menuHashtagName,
            'user' => $menuUserName,
            'post' => $menuPostListName,
            'more' => $fsLang['more'],
        ];
    }

    public static function urlArr(string $langTag): array
    {
        $configKeys = ConfigHelper::fresnsConfigByItemKeys([
            'site_url',
            'website_user_path',
            'website_post_path',
            'website_hashtag_path',
            'website_user_detail_path',
            'website_post_detail_path',
            'website_hashtag_detail_path',
            'default_language',
            'menu_user_status',
            'menu_hashtag_status',
            'menu_post_list_status',
        ]);

        $userUrl = $configKeys['site_url'].'/'.$langTag.'/'.$configKeys['website_user_path'];
        $hashtagUrl = $configKeys['site_url'].'/'.$langTag.'/'.$configKeys['website_hashtag_path'];
        $postUrl = $configKeys['site_url'].'/'.$langTag.$configKeys['website_post_path'].'/list';
        $userDetailUrl = $configKeys['site_url'].'/'.$langTag.'/'.$configKeys['website_user_detail_path'].'/';
        $hashtagDetailUrl = $configKeys['site_url'].'/'.$langTag.'/'.$configKeys['website_hashtag_detail_path'].'/';
        $postDetailUrl = $configKeys['site_url'].'/'.$langTag.'/'.$configKeys['website_post_detail_path'].'/';

        if ($configKeys['default_language'] == $langTag) {
            $userUrl = $configKeys['site_url'].'/'.$configKeys['website_user_path'];
            $hashtagUrl = $configKeys['site_url'].'/'.$configKeys['website_hashtag_path'];
            $postUrl = $configKeys['site_url'].'/'.$configKeys['website_post_path'].'/list';
            $userDetailUrl = $configKeys['site_url'].'/'.$configKeys['website_user_detail_path'].'/';
            $hashtagDetailUrl = $configKeys['site_url'].'/'.$configKeys['website_hashtag_detail_path'].'/';
            $postDetailUrl = $configKeys['site_url'].'/'.$configKeys['website_post_detail_path'].'/';
        }

        return [
            'user' => $configKeys['menu_user_status'] ? $userUrl : null,
            'hashtag' => $configKeys['menu_hashtag_status'] ? $hashtagUrl : null,
            'post' => $configKeys['menu_post_list_status'] ? $postUrl : null,
            'userDetail' => $userDetailUrl,
            'hashtagDetail' => $hashtagDetailUrl,
            'postDetail' => $postDetailUrl,
        ];
    }

    public static function saveHtml(string $langTag, mixed $html): void
    {
        Language::withTrashed()->updateOrCreate([
            'table_name' => 'configs',
            'table_column' => 'item_value',
            'table_key' => 'portal_4',
            'lang_tag' => $langTag,
        ], [
            'table_id' => null,
            'lang_content' => $html,
            'deleted_at' => null,
        ]);
    }
}
