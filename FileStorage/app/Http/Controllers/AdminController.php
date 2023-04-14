<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FileStorage\Http\Controllers;

use App\Helpers\FileHelper;
use App\Helpers\PluginHelper;
use App\Models\Config;
use App\Models\File;
use App\Models\FileUsage;
use App\Utilities\AppUtility;
use App\Utilities\FileUtility;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Plugins\FileStorage\Helpers\ConfigHelper;

class AdminController extends Controller
{
    public function index()
    {
        return redirect()->to(route('file-storage.admin.image'));
    }

    // image config
    public function adminImage()
    {
        $version = PluginHelper::fresnsPluginVersionByUnikey('FileStorage');
        $marketUrl = AppUtility::MARKETPLACE_URL.'/open-source';

        $configKeys = [
            'filestorage_image_driver',
            'filestorage_image_private_key',
            'filestorage_image_passphrase',
            'filestorage_image_host_fingerprint',
            'filestorage_image_processing_library',
            'filestorage_image_processing_params',
            'filestorage_image_watermark_file',
            'filestorage_image_watermark_config',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        $imageDriver = $configs->where('item_key', 'filestorage_image_driver')->first()?->item_value ?? 'local';
        $imagePrivateKey = $configs->where('item_key', 'filestorage_image_private_key')->first()?->item_value;
        $imagePassphrase = $configs->where('item_key', 'filestorage_image_passphrase')->first()?->item_value;
        $imageHostFingerprint = $configs->where('item_key', 'filestorage_image_host_fingerprint')->first()?->item_value;
        $imageProcessingLibrary = $configs->where('item_key', 'filestorage_image_processing_library')->first()?->item_value ?? 'gd';
        $imageProcessingParams = $configs->where('item_key', 'filestorage_image_processing_params')->first()?->item_value ?? [
            'config' => 400,
            'ratio' => 400,
            'square' => 200,
            'big' => 1500,
        ];
        $imageWatermarkFile = $configs->where('item_key', 'filestorage_image_watermark_file')->first()?->item_value;
        $imageWatermarkConfig = $configs->where('item_key', 'filestorage_image_watermark_config')->first()?->item_value ?? [
            'status' => 'close',
            'position' => 'center',
        ];

        $watermarkFile = null;
        $watermarkFileUrl = null;
        if ($imageWatermarkFile) {
            $watermarkFile = File::where('id', $imageWatermarkFile)->first();

            $watermarkFileUrl = $watermarkFile?->path ? Storage::url($watermarkFile?->path) : null;
        }

        $fileUsages = FileUsage::with(['file'])
            ->where('usage_type', FileUsage::TYPE_SYSTEM)
            ->where('table_name', 'configs')
            ->where('table_column', 'item_value')
            ->where('table_key', 'filestorage_image_watermark_file')
            ->latest()
            ->get();

        return view('FileStorage::admin-image', compact(
            'version',
            'marketUrl',
            'imageDriver',
            'imagePrivateKey',
            'imagePassphrase',
            'imageHostFingerprint',
            'imageProcessingLibrary',
            'imageProcessingParams',
            'watermarkFile',
            'watermarkFileUrl',
            'imageWatermarkConfig',
            'fileUsages',
        ));
    }

    // video config
    public function adminVideo()
    {
        $version = PluginHelper::fresnsPluginVersionByUnikey('FileStorage');
        $marketUrl = AppUtility::MARKETPLACE_URL.'/open-source';

        $configKeys = [
            'filestorage_video_driver',
            'filestorage_video_private_key',
            'filestorage_video_passphrase',
            'filestorage_video_host_fingerprint',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        $videoDriver = $configs->where('item_key', 'filestorage_video_driver')->first()?->item_value ?? 'local';
        $videoPrivateKey = $configs->where('item_key', 'filestorage_video_private_key')->first()?->item_value;
        $videoPassphrase = $configs->where('item_key', 'filestorage_video_passphrase')->first()?->item_value;
        $videoHostFingerprint = $configs->where('item_key', 'filestorage_video_host_fingerprint')->first()?->item_value;

        return view('FileStorage::admin-video', compact(
            'version',
            'marketUrl',
            'videoDriver',
            'videoPrivateKey',
            'videoPassphrase',
            'videoHostFingerprint',
        ));
    }

    // audio config
    public function adminAudio()
    {
        $version = PluginHelper::fresnsPluginVersionByUnikey('FileStorage');
        $marketUrl = AppUtility::MARKETPLACE_URL.'/open-source';

        $configKeys = [
            'filestorage_audio_driver',
            'filestorage_audio_private_key',
            'filestorage_audio_passphrase',
            'filestorage_audio_host_fingerprint',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        $audioDriver = $configs->where('item_key', 'filestorage_audio_driver')->first()?->item_value ?? 'local';
        $audioPrivateKey = $configs->where('item_key', 'filestorage_audio_private_key')->first()?->item_value;
        $audioPassphrase = $configs->where('item_key', 'filestorage_audio_passphrase')->first()?->item_value;
        $audioHostFingerprint = $configs->where('item_key', 'filestorage_audio_host_fingerprint')->first()?->item_value;

        return view('FileStorage::admin-audio', compact(
            'version',
            'marketUrl',
            'audioDriver',
            'audioPrivateKey',
            'audioPassphrase',
            'audioHostFingerprint',
        ));
    }

    // document config
    public function adminDocument()
    {
        $version = PluginHelper::fresnsPluginVersionByUnikey('FileStorage');
        $marketUrl = AppUtility::MARKETPLACE_URL.'/open-source';

        $configKeys = [
            'filestorage_document_driver',
            'filestorage_document_private_key',
            'filestorage_document_passphrase',
            'filestorage_document_host_fingerprint',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        $documentDriver = $configs->where('item_key', 'filestorage_document_driver')->first()?->item_value ?? 'local';
        $documentPrivateKey = $configs->where('item_key', 'filestorage_document_private_key')->first()?->item_value;
        $documentPassphrase = $configs->where('item_key', 'filestorage_document_passphrase')->first()?->item_value;
        $documentHostFingerprint = $configs->where('item_key', 'filestorage_document_host_fingerprint')->first()?->item_value;

        return view('FileStorage::admin-document', compact(
            'version',
            'marketUrl',
            'documentDriver',
            'documentPrivateKey',
            'documentPassphrase',
            'documentHostFingerprint',
        ));
    }

    // upload test
    public function adminTest()
    {
        $version = PluginHelper::fresnsPluginVersionByUnikey('FileStorage');

        $fileUsages = FileUsage::with(['file'])
            ->where('usage_type', FileUsage::TYPE_OTHER)
            ->where('table_name', 'plugins')
            ->where('table_column', 'unikey')
            ->where('table_key', 'FileStorage')
            ->latest()
            ->get();

        $files = [];
        foreach ($fileUsages as $fileUsage) {
            $files[] = FileHelper::fresnsFileInfoById($fileUsage->file_id);
        }

        return view('FileStorage::admin-test', compact('version', 'files'));
    }

    // admin update
    public function update(Request $request)
    {
        $fileTypeInt = match ($request->type) {
            'image' => File::TYPE_IMAGE,
            'video' => File::TYPE_VIDEO,
            'audio' => File::TYPE_AUDIO,
            'document' => File::TYPE_DOCUMENT,
            default => null,
        };

        if (empty($fileTypeInt)) {
            return back()->with('failure', __('FsLang::tips.requestFailure'));
        }

        $type = $request->type;

        if ($request->driver) {
            Config::updateOrCreate([
                'item_key' => "filestorage_{$type}_driver",
            ], [
                'item_value' => $request->driver,
                'item_type' => 'string',
                'item_tag' => 'filestorage',
            ]);
        }

        if ($request->privateKey) {
            Config::updateOrCreate([
                'item_key' => "filestorage_{$type}_private_key",
            ], [
                'item_value' => $request->privateKey,
                'item_type' => 'string',
                'item_tag' => 'filestorage',
            ]);
        }

        if ($request->passphrase) {
            Config::updateOrCreate([
                'item_key' => "filestorage_{$type}_passphrase",
            ], [
                'item_value' => $request->passphrase,
                'item_type' => 'string',
                'item_tag' => 'filestorage',
            ]);
        }

        if ($request->hostFingerprint) {
            Config::updateOrCreate([
                'item_key' => "filestorage_{$type}_host_fingerprint",
            ], [
                'item_value' => $request->hostFingerprint,
                'item_type' => 'string',
                'item_tag' => 'filestorage',
            ]);
        }

        // image config
        if ($request->imageProcessingLibrary) {
            Config::updateOrCreate([
                'item_key' => 'filestorage_image_processing_library',
            ], [
                'item_value' => $request->imageProcessingLibrary,
                'item_type' => 'string',
                'item_tag' => 'filestorage',
            ]);
        }

        if ($request->imageProcessingParams) {
            Config::updateOrCreate([
                'item_key' => 'filestorage_image_processing_params',
            ], [
                'item_value' => $request->imageProcessingParams,
                'item_type' => 'object',
                'item_tag' => 'filestorage',
            ]);
        }

        if ($request->imageWatermarkFile) {
            $bodyInfo = [
                'platformId' => 4,
                'usageType' => FileUsage::TYPE_SYSTEM,
                'tableName' => 'configs',
                'tableColumn' => 'item_value',
                'tableId' => null,
                'tableKey' => 'filestorage_image_watermark_file',
                'type' => File::TYPE_IMAGE,
                'disk' => 'local',
                'moreJson' => null,
            ];

            $fileInfo = FileUtility::uploadFile($bodyInfo, config('filesystems.disks.public'), $request->imageWatermarkFile);

            $file = File::where('fid', $fileInfo['fid'])->first();

            Config::updateOrCreate([
                'item_key' => 'filestorage_image_watermark_file',
            ], [
                'item_value' => $file?->id,
                'item_type' => 'file',
                'item_tag' => 'filestorage',
            ]);
        }

        if ($request->imageWatermarkConfig) {
            Config::updateOrCreate([
                'item_key' => 'filestorage_image_watermark_config',
            ], [
                'item_value' => $request->imageWatermarkConfig,
                'item_type' => 'object',
                'item_tag' => 'filestorage',
            ]);
        }

        ConfigHelper::forgetCache($fileTypeInt);

        return back()->with('success', __('FsLang::tips.updateSuccess'));
    }

    // upload file
    public function uploadFile(Request $request)
    {
        $type = match ($request->type) {
            'image' => File::TYPE_IMAGE,
            'video' => File::TYPE_VIDEO,
            'audio' => File::TYPE_AUDIO,
            'document' => File::TYPE_DOCUMENT,
            default => null,
        };

        if (empty($type) || empty($request->file)) {
            return back()->with('failure', __('FsLang::tips.requestFailure'));
        }

        $wordBody = [
            'platformId' => 4,
            'usageType' => FileUsage::TYPE_OTHER,
            'tableName' => 'plugins',
            'tableColumn' => 'unikey',
            'tableId' => null,
            'tableKey' => 'FileStorage',
            'type' => $type,
            'disk' => 'local',
            'moreJson' => null,
            'file' => $request->file,
        ];

        $fresnsResp = \FresnsCmdWord::plugin('FileStorage')->uploadFile($wordBody);

        if ($fresnsResp->isErrorResponse()) {
            return back()->with('failure', $fresnsResp->getMessage().' >> '.implode(' -- ', $fresnsResp->getData()));
        }

        return back()->with('success', $fresnsResp->getMessage());
    }

    // delete file
    public function deleteFile(int $type, string $fid)
    {
        $wordBody = [
            'type' => $type,
            'fileIdsOrFids' => (array) $fid,
        ];

        $fresnsResp = \FresnsCmdWord::plugin('FileStorage')->physicalDeletionFiles($wordBody);

        if ($fresnsResp->isErrorResponse()) {
            return back()->with('failure', $fresnsResp->getMessage().' >> '.implode(' -- ', $fresnsResp->getData()));
        }

        return back()->with('success', $fresnsResp->getMessage());
    }
}
