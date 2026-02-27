<?php

namespace App\Helper;

use App\Enums\UploadDataType;

class MimeHelper
{
    public static function convertMimeType(string $mimeType): string
    {
        $extensionType = match ($mimeType) {
            // Documents
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'text/plain' => 'txt',
            'text/csv' => 'csv',
            'text/html' => 'html',

            // Images
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/heic' => 'heic',
            'image/heif' => 'heif',

            // Audio
            'audio/mpeg' => 'mp3',
            'audio/wav' => 'wav',
            'audio/ogg' => 'ogg',
            'audio/aac' => 'aac',
            'audio/flac' => 'flac',

            // Video
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            'video/ogg' => 'ogv',
            'video/quicktime' => 'mov',
            'video/x-msvideo' => 'avi',
            'video/x-matroska' => 'mkv',

            // Archives
            'application/zip' => 'zip',
            'application/x-tar' => 'tar',
            'application/gzip' => 'gz',
            'application/x-7z-compressed' => '7z',
            'application/x-rar-compressed' => 'rar',

            // Code / data
            'application/json' => 'json',
            'application/xml' => 'xml',
            'text/xml' => 'xml',
            'application/yaml' => 'yaml',
            'text/yaml' => 'yaml',

            default => '',
        };

        return $extensionType ?: $mimeType;
    }

    public static function identifyUploadDataType(string $mimeType): UploadDataType
    {
        $extension = self::convertMimeType($mimeType);

        return match (true) {
            in_array($extension, ['jpg', 'png', 'gif', 'webp', 'svg', 'heic', 'heif']) => UploadDataType::IMAGE,
            in_array($extension, ['mp4', 'webm', 'ogv', 'mov', 'avi', 'mkv']) => UploadDataType::VIDEO,
            in_array($extension, ['mp3', 'wav', 'ogg', 'aac', 'flac']) => UploadDataType::AUDIO,
            in_array($extension, ['zip', 'tar', 'gz', '7z', 'rar']) => UploadDataType::ARCHIVE,
            in_array($extension, ['json', 'xml', 'yaml', 'html', 'csv']) => UploadDataType::CODE,
            in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt']) => UploadDataType::FILE,
            default => UploadDataType::OTHER,
        };
    }
}
