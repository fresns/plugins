<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\SharePoster\Http\Controllers;

use App\Helpers\CacheHelper;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function index()
    {
        $configModel = Config::where('item_key', 'shareposter_config')->first()?->item_value;

        $config = $configModel['user'] ?? [];

        $background_url = '/assets/SharePoster/user.jpg';

        if ($config['background_path']) {
            $disk = Storage::disk('public');

            $background_url = $disk->url($config['background_path']);
        }

        return view('SharePoster::user', compact('config', 'background_url'));
    }

    public function group()
    {
        $configModel = Config::where('item_key', 'shareposter_config')->first()?->item_value;

        $config = $configModel['group'] ?? [];

        $background_url = '/assets/SharePoster/group.jpg';

        if ($config['background_path']) {
            $disk = Storage::disk('public');

            $background_url = $disk->url($config['background_path']);
        }

        return view('SharePoster::group', compact('config', 'background_url'));
    }

    public function hashtag()
    {
        $configModel = Config::where('item_key', 'shareposter_config')->first()?->item_value;

        $config = $configModel['hashtag'] ?? [];

        $background_url = '/assets/SharePoster/hashtag.jpg';

        if ($config['background_path']) {
            $disk = Storage::disk('public');

            $background_url = $disk->url($config['background_path']);
        }

        return view('SharePoster::hashtag', compact('config', 'background_url'));
    }

    public function post()
    {
        $configModel = Config::where('item_key', 'shareposter_config')->first()?->item_value;

        $config = $configModel['post'] ?? [];

        $background_url = '/assets/SharePoster/post.jpg';

        if ($config['background_path']) {
            $disk = Storage::disk('public');

            $background_url = $disk->url($config['background_path']);
        }

        return view('SharePoster::post', compact('config', 'background_url'));
    }

    public function comment()
    {
        $configModel = Config::where('item_key', 'shareposter_config')->first()?->item_value;

        $config = $configModel['comment'] ?? [];

        $background_url = '/assets/SharePoster/comment.jpg';

        if ($config['background_path']) {
            $disk = Storage::disk('public');

            $background_url = $disk->url($config['background_path']);
        }

        return view('SharePoster::comment', compact('config', 'background_url'));
    }

    public function font()
    {
        $configModel = Config::where('item_key', 'shareposter_config')->first()?->item_value;

        $config = $configModel ?? [];

        return view('SharePoster::font', compact('config'));
    }

    public function update(Request $request)
    {
        $configModel = Config::where('item_key', 'shareposter_config')->first();

        $configArr = $configModel?->item_value ?? [];

        CacheHelper::forgetFresnsConfigs(['shareposter_config']);

        if ($request->fontFile) {
            $disk = Storage::disk('public');
            $directoryPath = 'share-poster/font';

            // Get the original file extension
            $extension = $request->fontFile->getClientOriginalExtension();

            // Create a new file name
            $filename = uniqid().'.'.$extension;

            $diskPath = $disk->putFileAs($directoryPath, $request->fontFile, $filename);

            $configArr['fontPath'] = $diskPath;

            $configModel->update([
                'item_value' => $configArr,
            ]);

            return $this->updateSuccess();
        }

        if ($request->resetFont) {
            $configArr['fontPath'] = '';

            $configModel->update([
                'item_value' => $configArr,
            ]);

            return $this->updateSuccess();
        }

        $type = $request->type;

        $config = $configArr[$type] ?? [];

        if ($request->background_file) {
            $disk = Storage::disk('public');
            $directoryPath = 'share-poster/background';

            $diskPath = $disk->putFile($directoryPath, $request->background_file);

            $config['background_path'] = $diskPath;
        }

        $config['cache'] = (bool) $request->cache;
        $config['avatar_size'] = (int) $request->avatar_size;
        $config['avatar_circle'] = (bool) $request->avatar_circle;
        $config['avatar_x_position'] = (int) $request->avatar_x_position;
        $config['avatar_y_position'] = (int) $request->avatar_y_position;
        $config['nickname_color'] = $request->nickname_color;
        $config['nickname_font_size'] = (int) $request->nickname_font_size;
        $config['nickname_x_center'] = (bool) $request->nickname_x_center;
        $config['nickname_x_position'] = (int) $request->nickname_x_position;
        $config['nickname_y_position'] = (int) $request->nickname_y_position;
        $config['bio_color'] = $request->bio_color;
        $config['bio_font_size'] = (int) $request->bio_font_size;
        $config['bio_x_position'] = (int) $request->bio_x_position;
        $config['bio_y_position'] = (int) $request->bio_y_position;
        $config['bio_max_width'] = (int) $request->bio_max_width;
        $config['bio_max_lines'] = (int) $request->bio_max_lines;
        $config['bio_line_spacing'] = (int) $request->bio_line_spacing;
        $config['title_color'] = $request->title_color;
        $config['title_font_size'] = (int) $request->title_font_size;
        $config['title_x_position'] = (int) $request->title_x_position;
        $config['title_y_position'] = (int) $request->title_y_position;
        $config['title_max_width'] = (int) $request->title_max_width;
        $config['title_max_lines'] = (int) $request->title_max_lines;
        $config['title_line_spacing'] = (int) $request->title_line_spacing;
        $config['content_color'] = $request->content_color;
        $config['content_font_size'] = (int) $request->content_font_size;
        $config['content_x_position'] = (int) $request->content_x_position;
        $config['content_y_position'] = (int) $request->content_y_position;
        $config['content_max_width'] = (int) $request->content_max_width;
        $config['content_max_lines'] = (int) $request->content_max_lines;
        $config['content_line_spacing'] = (int) $request->content_line_spacing;
        $config['qrcode_size'] = (int) $request->qrcode_size;
        $config['qrcode_x_position'] = (int) $request->qrcode_x_position;
        $config['qrcode_y_position'] = (int) $request->qrcode_y_position;
        $config['qrcode_bottom_margin'] = (int) $request->qrcode_bottom_margin;

        $configArr[$type] = $config;

        $configModel->update([
            'item_value' => $configArr,
        ]);

        return $this->updateSuccess();
    }
}
