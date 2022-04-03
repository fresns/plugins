<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DomainsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('domains')->delete();
        
        \DB::table('domains')->insert(array (
            0 => 
            array (
                'id' => 1,
                'domain' => 'fresns.com',
                'sld' => 'fresns.com',
                'icon_file_id' => NULL,
                'icon_file_url' => NULL,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2021-10-08 10:00:00',
                'updated_at' => '2021-10-08 10:00:00',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'domain' => 'fresns.org',
                'sld' => 'fresns.org',
                'icon_file_id' => NULL,
                'icon_file_url' => NULL,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2021-10-08 10:00:00',
                'updated_at' => '2021-10-08 10:00:00',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'domain' => 'fresns.org',
                'sld' => 'docs.fresns.org',
                'icon_file_id' => NULL,
                'icon_file_url' => NULL,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2021-10-08 10:00:00',
                'updated_at' => '2021-10-08 10:00:00',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'domain' => 'fresns.org',
                'sld' => 'apps.fresns.org',
                'icon_file_id' => NULL,
                'icon_file_url' => NULL,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2021-10-08 10:00:00',
                'updated_at' => '2021-10-08 10:00:00',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'domain' => 'fresns.org',
                'sld' => 'discuss.fresns.org',
                'icon_file_id' => NULL,
                'icon_file_url' => NULL,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2021-10-08 10:00:00',
                'updated_at' => '2021-10-08 10:00:00',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'domain' => 'fresns.cn',
                'sld' => 'fresns.cn',
                'icon_file_id' => NULL,
                'icon_file_url' => NULL,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2021-10-08 10:00:00',
                'updated_at' => '2021-10-08 10:00:00',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'domain' => 'fresns.cn',
                'sld' => 'docs.fresns.cn',
                'icon_file_id' => NULL,
                'icon_file_url' => NULL,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2021-10-08 10:00:00',
                'updated_at' => '2021-10-08 10:00:00',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'domain' => 'fresns.cn',
                'sld' => 'apps.fresns.cn',
                'icon_file_id' => NULL,
                'icon_file_url' => NULL,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2021-10-08 10:00:00',
                'updated_at' => '2021-10-08 10:00:00',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'domain' => 'fresns.cn',
                'sld' => 'discuss.fresns.cn',
                'icon_file_id' => NULL,
                'icon_file_url' => NULL,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2021-10-08 10:00:00',
                'updated_at' => '2021-10-08 10:00:00',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'domain' => 'tangjie.me',
                'sld' => 'tangjie.me',
                'icon_file_id' => NULL,
                'icon_file_url' => NULL,
                'post_count' => 0,
                'comment_count' => 0,
                'is_enable' => 1,
                'created_at' => '2021-10-08 10:00:00',
                'updated_at' => '2021-10-08 10:00:00',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}