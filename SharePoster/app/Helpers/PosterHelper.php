<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\SharePoster\Helpers;

use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Helpers\LanguageHelper;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;
use ImagickDraw;
use ImagickPixel;

class PosterHelper
{
    // generatePoster
    public static function generatePoster(string $type, mixed $model, string $langTag): ?string
    {
        if (empty($model)) {
            return null;
        }

        $config = ConfigHelper::fresnsConfigByItemKey('shareposter_config');

        $configArr = $config[$type];

        if (empty($configArr)) {
            return null;
        }

        $font_path = $config['fontPath'] ? storage_path('app/public/'.$config['fontPath']) : public_path('assets/SharePoster/arial-unicode-ms.ttf');

        $avatar_size = $configArr['avatar_size'] ?? 200;
        $avatar_circle = $configArr['avatar_circle'] ?? true;
        $avatar_x_position = $configArr['avatar_x_position'] ?? 0;
        $avatar_y_position = $configArr['avatar_y_position'] ?? 0;

        $nickname_color = $configArr['nickname_color'] ?? '#414141';
        $nickname_font_size = $configArr['nickname_font_size'] ?? 62;
        $nickname_x_center = $configArr['nickname_x_center'] ?? true;
        $nickname_x_position = $configArr['nickname_x_position'] ?? 0;
        $nickname_y_position = $configArr['nickname_y_position'] ?? 0;

        $bio_x_position = $configArr['bio_x_position'] ?? 0;
        $bio_y_position = $configArr['bio_y_position'] ?? 0;
        $bio_color = $configArr['bio_color'] ?? '#7c7c7c';
        $bio_font_size = $configArr['bio_font_size'] ?? 36;
        $bio_max_width = $configArr['bio_max_width'] ?? 0;
        $bio_max_lines = $configArr['bio_max_lines'] ?? 1;
        $bio_line_spacing = $configArr['bio_line_spacing'] ?? 12;

        $title_x_position = $configArr['title_x_position'] ?? 0;
        $title_y_position = $configArr['title_y_position'] ?? 0;
        $title_color = $configArr['title_color'] ?? '#343434';
        $title_font_size = $configArr['title_font_size'] ?? 44;
        $title_x_center = $configArr['title_x_center'] ?? false;
        $title_max_width = $configArr['title_max_width'] ?? 0;
        $title_max_lines = $configArr['title_max_lines'] ?? 1;
        $title_line_spacing = $configArr['title_line_spacing'] ?? 12;

        $content_x_position = $configArr['content_x_position'] ?? 0;
        $content_y_position = $configArr['content_y_position'] ?? 0;
        $content_color = $configArr['content_color'] ?? '#343434';
        $content_font_size = $configArr['content_font_size'] ?? 48;
        $content_max_width = $configArr['content_max_width'] ?? 0;
        $content_max_lines = $configArr['content_max_lines'] ?? 20;
        $content_line_spacing = $configArr['content_line_spacing'] ?? 12;

        $qrcode_size = $configArr['qrcode_size'] ?? 200;
        $qrcode_x_position = $configArr['qrcode_x_position'] ?? 0;
        $qrcode_y_position = $configArr['qrcode_y_position'] ?? 0;
        $qrcode_bottom_margin = $configArr['qrcode_bottom_margin'] ?? 0;

        $background_path = $configArr['background_path'] ? storage_path('app/public/'.$configArr['background_path']) : public_path("assets/SharePoster/{$type}.jpg");
        $avatar = null;
        $nickname = null;
        $bio = null;
        $title = null;
        $content = null;
        $url = null;

        $configKeys = ConfigHelper::fresnsConfigByItemKeys([
            'site_url',
            'user_identifier',
            'website_user_detail_path',
            'website_group_detail_path',
            'website_hashtag_detail_path',
            'website_post_detail_path',
            'website_comment_detail_path',
        ]);

        $siteUrl = $configKeys['site_url'] ?? config('app.url');

        switch ($type) {
            case 'user':
                $avatar = $model->getUserAvatar();
                $nickname = $model->nickname;
                $bio = $model->bio;

                $profileFsid = $model->username;
                $userUrl = $siteUrl.'/'.$configKeys['website_user_detail_path'].'/'.$model->username;

                if ($configKeys['user_identifier'] == 'uid') {
                    $profileFsid = $model->uid;
                    $userUrl = $siteUrl.'/'.$configKeys['website_user_detail_path'].'/'.$model->uid;
                }

                $title = '@'.$profileFsid;
                $content = '';
                $url = $userUrl;
                break;

            case 'group':
                $avatar = FileHelper::fresnsFileUrlByTableColumn($model->cover_file_id, $model->cover_file_url);
                $nickname = LanguageHelper::fresnsLanguageByTableId('groups', 'name', $model->id, $langTag) ?? $model->name;
                $bio = LanguageHelper::fresnsLanguageByTableId('groups', 'description', $model->id, $langTag) ?? $model->description;
                $title = '';
                $content = '';
                $url = $siteUrl.'/'.$configKeys['website_group_detail_path'].'/'.$model->gid;
                break;

            case 'hashtag':
                $avatar = FileHelper::fresnsFileUrlByTableColumn($model->cover_file_id, $model->cover_file_url);
                $nickname = $model->name;
                $bio = LanguageHelper::fresnsLanguageByTableId('hashtags', 'description', $model->id, $langTag) ?? $model->description;
                $title = '';
                $content = '';
                $url = $siteUrl.'/'.$configKeys['website_hashtag_detail_path'].'/'.$model->slug;
                break;

            case 'post':
                $avatar = $model->author->getUserAvatar();
                $nickname = $model->author->nickname;
                $bio = $model->author->bio;
                $title = $model->title;
                $content = $model->is_markdown ? strip_tags(Str::markdown($model->content)) : $model->content; // Removing HTML tags
                $url = $siteUrl.'/'.$configKeys['website_post_detail_path'].'/'.$model->pid;
                break;

            case 'comment':
                $avatar = $model->author->getUserAvatar();
                $nickname = $model->author->nickname;
                $bio = $model->author->bio;
                $title = '';
                $content = $model->is_markdown ? strip_tags(Str::markdown($model->content)) : $model->content; // Removing HTML tags
                $url = $siteUrl.'/'.$configKeys['website_comment_detail_path'].'/'.$model->cid;
                break;

            default:
                return null;
                break;
        }

        // 1. background
        $background = new Imagick($background_path);

        // 2. user avatar
        if ($avatar && $avatar_x_position && $avatar_y_position) {
            $avatarImagick = new Imagick($avatar);

            $size = $avatarImagick->getImageWidth(); // Get avatar width and height

            if ($avatar_circle) {
                $shape = new ImagickDraw();
                $shape->setFillColor('white');
                $shape->circle($size / 2, $size / 2, $size / 2, 0);

                $mask = new Imagick(); // Create mask for circular avatar
                $mask->newImage($size, $size, new ImagickPixel('transparent'));
                $mask->drawImage($shape);
            } else {
                $shape = new ImagickDraw();
                $shape->setFillColor('white');
                $shape->roundRectangle(0, 0, $size, $size, 10, 10); // 10px border-radius

                $mask = new Imagick(); // Create mask for square avatar with border-radius
                $mask->newImage($size, $size, new ImagickPixel('transparent'));
                $mask->drawImage($shape);
            }

            $roundedImage = new Imagick(); // Create new transparent image of avatar size
            $roundedImage->newImage($size, $size, new ImagickPixel('transparent'));
            $roundedImage->compositeImage($avatarImagick, Imagick::COMPOSITE_OVER, 0, 0); // Merge avatar onto new image
            $roundedImage->compositeImage($mask, Imagick::COMPOSITE_DSTIN, 0, 0);
            $roundedImage->resizeImage($avatar_size, $avatar_size, Imagick::FILTER_LANCZOS, 1); // Resize the image

            $background->compositeImage($roundedImage, Imagick::COMPOSITE_OVER, $avatar_x_position, $avatar_y_position); // Merge rounded avatar onto background
        }

        // 3. user nickname
        if ($nickname && $nickname_y_position) {
            $nicknameDraw = new ImagickDraw();
            $nicknameDraw->setFillColor($nickname_color);
            $nicknameDraw->setFont($font_path);
            $nicknameDraw->setFontSize($nickname_font_size);

            $nickname_new_x_position = $nickname_x_position;
            if ($nickname_x_center) {
                // Calculate the width of the text
                $nicknameMetrics = $background->queryFontMetrics($nicknameDraw, $nickname);
                $nicknameWidth = $nicknameMetrics['textWidth'];

                // Calculate the x position for centering
                $backgroundWidth = $background->getImageWidth();

                $nickname_new_x_position = ($backgroundWidth - $nicknameWidth) / 2;
            }

            $background->annotateImage($nicknameDraw, $nickname_new_x_position, $nickname_y_position, 0, $nickname);
        }

        // 4. user bio
        if ($bio && $bio_x_position && $bio_y_position) {
            $bioDraw = new ImagickDraw();
            $bioDraw->setFillColor($bio_color);
            $bioDraw->setFont($font_path);
            $bioDraw->setFontSize($bio_font_size);
            $bioDraw->setTextInterlineSpacing($bio_line_spacing);

            $newBio = $bio;
            if ($bio_max_width) {
                $wrappedBio = self::wrapText($background, $bioDraw, $bio, $bio_max_width, $bio_max_lines);

                $newBio = $wrappedBio['text'];
            }

            $background->annotateImage($bioDraw, $bio_x_position, $bio_y_position, 0, $newBio);
        }

        // 5. title
        if ($title && $title_y_position) {
            $titleDraw = new ImagickDraw();
            $titleDraw->setFillColor($title_color);
            $titleDraw->setFont($font_path);
            $titleDraw->setFontSize($title_font_size);
            $titleDraw->setTextInterlineSpacing($title_line_spacing);

            $title_new_x_position = $title_x_position;
            if ($title_x_center) {
                // Calculate the width of the text
                $titleMetrics = $background->queryFontMetrics($titleDraw, $title);
                $titleWidth = $titleMetrics['textWidth'];

                // Calculate the x position for centering
                $backgroundWidth = $background->getImageWidth();

                $title_new_x_position = ($backgroundWidth - $titleWidth) / 2;
            }

            $newTitle = $title;
            if ($title_max_width) {
                $wrappedTitle = self::wrapText($background, $titleDraw, $title, $title_max_width, $title_max_lines);

                $newTitle = $wrappedTitle['text'];
            }

            $background->annotateImage($titleDraw, $title_new_x_position, $title_y_position, 0, $newTitle);
        }

        // 6. content
        if ($content && $content_x_position && $content_y_position) {
            $contentDraw = new ImagickDraw();
            $contentDraw->setFillColor($content_color);
            $contentDraw->setFont($font_path);
            $contentDraw->setFontSize($content_font_size);
            $contentDraw->setTextInterlineSpacing($content_line_spacing);

            $newContent = $content;
            if ($content_max_width) {
                $wrappedContent = self::wrapText($background, $contentDraw, $content, $content_max_width, $content_max_lines);

                $newContent = $wrappedContent['text'];
            }

            // Calculate total content height
            $tempImage = new Imagick(); // Create a transparent temp image
            $tempImage->newImage($content_max_width, 10000, new ImagickPixel('transparent')); // 10000 is an estimated height
            $tempImage->annotateImage($contentDraw, 0, $content_font_size, 0, $newContent); // Draw text using annotateImage

            $tempImage->trimImage(0); // Trim image to remove transparency
            $geometry = $tempImage->getImageGeometry(); // Get dimensions after trimming
            $contentHeight = $geometry['height']; // Get text height

            $tempImage->clear(); // Clear temp image
            $tempImage->destroy(); // Clear temp image

            // Extract a region from the original background as a "tile background"
            $tileHeight = $content_font_size + $content_line_spacing; // Height per line (font size + spacing)
            $tileBackground = $background->clone();
            $tileBackground->cropImage($background->getImageWidth(), $tileHeight, 0, $content_y_position);

            // Duplicate the "tile background" based on content height
            $tilesNeeded = ceil($contentHeight / $tileHeight);
            $extendedBackground = new Imagick();
            for ($i = 0; $i < $tilesNeeded; $i++) {
                $extendedBackground->addImage($tileBackground);
            }
            $extendedBackground->resetIterator();
            $combined = $extendedBackground->appendImages(true);

            // Upper part
            $upperPart = $background->clone();
            $upperPart->cropImage($background->getImageWidth(), $content_y_position, 0, 0);

            // Lower part
            $lowerPartStartY = $content_y_position + $tileHeight;
            $lowerPart = $background->clone();
            $lowerPart->cropImage($background->getImageWidth(), $background->getImageHeight() - $lowerPartStartY, 0, $lowerPartStartY);

            // Create a new background image
            $newBackground = new Imagick();
            $newBackground->newImage($background->getImageWidth(), $upperPart->getImageHeight() + $contentHeight + $lowerPart->getImageHeight(), new ImagickPixel('transparent'));
            $newBackground->setImageFormat('jpg');  // Set format

            $newBackground->compositeImage($upperPart, Imagick::COMPOSITE_OVER, 0, 0);
            $newBackground->compositeImage($combined, Imagick::COMPOSITE_OVER, 0, $upperPart->getImageHeight());
            $newBackground->compositeImage($lowerPart, Imagick::COMPOSITE_OVER, 0, $upperPart->getImageHeight() + $contentHeight);

            $background->clear();
            $background->destroy();
            $background = $newBackground;

            $background->annotateImage($contentDraw, $content_x_position, $content_y_position, 0, $newContent);
        }

        // 7. qrcode
        if ($url && $qrcode_size) {
            $qrCodeImage = new Imagick();
            $qrCodeImage->readImageBlob(self::generateQrCode($url, $qrcode_size));

            // Calculate QR code's Y position to maintain a specified distance from the bottom
            if ($qrcode_bottom_margin) {
                $qrcode_y_position = $background->getImageHeight() - $qrcode_size - $qrcode_bottom_margin;
            }

            $background->compositeImage($qrCodeImage, Imagick::COMPOSITE_OVER, $qrcode_x_position, $qrcode_y_position);
        }

        // 8. poster save
        $backgroundString = $background->getImageBlob();

        $filePath = self::getPosterPath($type, $model);

        $disk = Storage::disk('public');
        $disk->put($filePath, $backgroundString);

        return $disk->url($filePath);
    }

    // generateQrCode
    public static function generateQrCode(string $url, ?int $size = 200): string
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size($size)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->validateResult(false)
            ->build();

        return $result->getString();
    }

    public static function wrapText(Imagick $imagick, ImagickDraw $draw, string $text, int $maxWidth, ?int $maxLines = null): array
    {
        $lines = [];
        $line = '';
        $chars = preg_split('//u', $text, null, PREG_SPLIT_NO_EMPTY);

        foreach ($chars as $char) {
            $testLine = $line.$char;
            $metrics = $imagick->queryFontMetrics($draw, $testLine);
            $testWidth = $metrics['textWidth'];

            if ($testWidth > $maxWidth) {
                $lines[] = $line;
                $line = $char;
            } else {
                $line .= $char;
            }
        }

        if ($line) {
            $lines[] = $line;
        }

        $textOutput = implode("\n", $lines);
        $actualLines = substr_count($textOutput, "\n") + 1; // Count line breaks and add 1 for total lines
        $newLines = $actualLines;

        if ($maxLines && $actualLines > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
            $end = end($lines);
            do {
                $end = mb_substr($end, 0, -1);
                $testEnd = $end.'...';
                $metrics = $imagick->queryFontMetrics($draw, $testEnd);
            } while ($metrics['textWidth'] > $maxWidth && mb_strlen($end) > 1);

            $lines[$maxLines - 1] = $testEnd; // Replace last line with ellipsis
            $textOutput = implode("\n", $lines); // Regenerate text output
            $newLines = substr_count($textOutput, "\n") + 1; // Recount line breaks and add 1 for total lines
        }

        return [
            'text' => $textOutput,
            'actualLines' => $actualLines,
            'newLines' => $newLines,
        ];
    }

    // getPosterPath
    public static function getPosterPath(string $type, mixed $model): string
    {
        $directoryPath = "share-poster/{$type}/{YYYYMM}/{DD}/";

        $replaceUseTypeDir = str_replace(
            ['{YYYYMM}', '{DD}'],
            [date('Ym'), date('d')],
            $directoryPath
        );

        $fileName = match ($type) {
            'user' => $model->uid.'-'.$model->updated_at->timestamp,
            'group' => $model->gid,
            'hashtag' => md5($model->slug),
            'post' => $model->pid.'-'.$model?->postAppend?->edit_count,
            'comment' => $model->cid.'-'.$model?->postAppend?->edit_count,
        };

        $filePath = "{$replaceUseTypeDir}{$fileName}.jpg";

        return $filePath;
    }
}
