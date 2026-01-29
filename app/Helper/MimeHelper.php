<?php

namespace App\Helper;

class MimeHelper
{
    public static function convertMimeType(string $mimeType): string
    {
        $extentiontype = match ($mimeType) {
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

        return $extentiontype ?: $mimeType;
    }
}
