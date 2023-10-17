<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Utilities\ConfigUtility;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $fresnsConfigItems = [
        [
            'item_key' => 'shareposter_config',
            'item_value' => '{"fontPath":"","user":{"background_path":"","cache":true,"avatar_size":290,"avatar_circle":true,"avatar_x_position":476,"avatar_y_position":326,"nickname_color":"#414141","nickname_font_size":62,"nickname_x_center":true,"nickname_x_position":0,"nickname_y_position":750,"bio_color":"#7c7c7c","bio_font_size":44,"bio_x_position":260,"bio_y_position":910,"bio_max_width":730,"bio_max_lines":4,"bio_line_spacing":12,"title_color":"#5c5c5c","title_font_size":40,"title_x_center":true,"title_x_position":0,"title_y_position":820,"title_max_width":0,"title_max_lines":0,"title_line_spacing":0,"content_color":null,"content_font_size":0,"content_x_position":0,"content_y_position":0,"content_max_width":0,"content_max_lines":0,"content_line_spacing":0,"qrcode_size":280,"qrcode_x_position":480,"qrcode_y_position":1350,"qrcode_bottom_margin":0},"group":{"background_path":"","cache":true,"avatar_size":296,"avatar_circle":false,"avatar_x_position":474,"avatar_y_position":170,"nickname_color":"#414141","nickname_font_size":62,"nickname_x_center":true,"nickname_x_position":0,"nickname_y_position":600,"bio_color":"#7c7c7c","bio_font_size":44,"bio_x_position":280,"bio_y_position":720,"bio_max_width":670,"bio_max_lines":6,"bio_line_spacing":12,"title_color":null,"title_font_size":0,"title_x_center":false,"title_x_position":0,"title_y_position":0,"title_max_width":0,"title_max_lines":0,"title_line_spacing":0,"content_color":null,"content_font_size":0,"content_x_position":0,"content_y_position":0,"content_max_width":0,"content_max_lines":0,"content_line_spacing":0,"qrcode_size":220,"qrcode_x_position":750,"qrcode_y_position":1140,"qrcode_bottom_margin":0},"hashtag":{"background_path":"","cache":true,"avatar_size":296,"avatar_circle":false,"avatar_x_position":474,"avatar_y_position":170,"nickname_color":"#414141","nickname_font_size":62,"nickname_x_center":true,"nickname_x_position":0,"nickname_y_position":600,"bio_color":"#7c7c7c","bio_font_size":44,"bio_x_position":280,"bio_y_position":720,"bio_max_width":670,"bio_max_lines":6,"bio_line_spacing":12,"title_color":null,"title_font_size":0,"title_x_center":false,"title_x_position":0,"title_y_position":0,"title_max_width":0,"title_max_lines":0,"title_line_spacing":0,"content_color":null,"content_font_size":0,"content_x_position":0,"content_y_position":0,"content_max_width":0,"content_max_lines":0,"content_line_spacing":0,"qrcode_size":220,"qrcode_x_position":750,"qrcode_y_position":1140,"qrcode_bottom_margin":0},"post":{"background_path":"","cache":true,"avatar_size":190,"avatar_circle":true,"avatar_x_position":70,"avatar_y_position":60,"nickname_color":"#333333","nickname_font_size":40,"nickname_x_center":false,"nickname_x_position":110,"nickname_y_position":320,"bio_color":"#7c7c7c","bio_font_size":36,"bio_x_position":110,"bio_y_position":400,"bio_max_width":1020,"bio_max_lines":1,"bio_line_spacing":12,"title_color":"#2c2c2c","title_font_size":52,"title_x_center":false,"title_x_position":110,"title_y_position":500,"title_max_width":1020,"title_max_lines":1,"title_line_spacing":12,"content_color":"#2c2c2c","content_font_size":48,"content_x_position":110,"content_y_position":600,"content_max_width":1020,"content_max_lines":20,"content_line_spacing":12,"qrcode_size":220,"qrcode_x_position":920,"qrcode_y_position":1210,"qrcode_bottom_margin":200},"comment":{"background_path":"","cache":true,"avatar_size":190,"avatar_circle":true,"avatar_x_position":70,"avatar_y_position":60,"nickname_color":"#414141","nickname_font_size":40,"nickname_x_center":false,"nickname_x_position":110,"nickname_y_position":320,"bio_color":"#7c7c7c","bio_font_size":44,"bio_x_position":110,"bio_y_position":400,"bio_max_width":1020,"bio_max_lines":1,"bio_line_spacing":12,"title_color":null,"title_font_size":0,"title_x_center":false,"title_x_position":0,"title_y_position":0,"title_max_width":0,"title_max_lines":0,"title_line_spacing":0,"content_color":"#343434","content_font_size":48,"content_x_position":110,"content_y_position":490,"content_max_width":1020,"content_max_lines":20,"content_line_spacing":12,"qrcode_size":220,"qrcode_x_position":920,"qrcode_y_position":1210,"qrcode_bottom_margin":200}}',
            'item_type' => 'object',
            'item_tag' => 'shareposter',
        ],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        ConfigUtility::addFresnsConfigItems($this->fresnsConfigItems);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        ConfigUtility::removeFresnsConfigItems($this->fresnsConfigItems);
    }
};
