<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CustomTwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('filesize', [$this, 'formatFileSize']),
            new TwigFilter('simple_format', [$this, 'simpleFormat']),
            new TwigFilter('name_file', [$this, 'nameFile']),
            new TwigFilter('format_size', [$this, 'formatSize']),
            new TwigFilter('type_file', [$this, 'typeFile']),
        ];
    }

    public function formatFileSize(int $size): string
    {
        if ($size >= 1073741824) {
            $size = number_format($size / 1073741824, 2) . ' GB';
        } elseif ($size >= 1048576) {
            $size = number_format($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            $size = number_format($size / 1024, 2) . ' KB';
        } else {
            $size = $size . ' bytes';
        }

        return $size;
    }

    public function simpleFormat(string $mimeType): string
    {
        $subTypeMap = [
            'pdf' => 'PDF',
            'x-pdf' => 'PDF',
            'png' => 'PNG',
            'jpeg' => 'JPEG',
            'jpg' => 'JPEG',
            'msword' => 'Word',
            'vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word',
            'vnd.ms-excel' => 'Excel',
            'vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Excel',
            'vnd.ms-powerpoint' => 'PowerPoint',
            'vnd.openxmlformats-officedocument.presentationml.presentation' => 'PowerPoint',
            'plain' => 'Texte',
            'csv' => 'CSV',
            'zip' => 'ZIP',
            'x-rar-compressed' => 'RAR',
            'x-7z-compressed' => '7-Zip',
            'x-tar' => 'TAR',
            'mpeg' => 'MP3',
            'wav' => 'WAV',
            'mp4' => 'MP4',
            'x-msvideo' => 'AVI',
        ];

        $parts = explode('/', $mimeType);
        $subType = end($parts);

        return $subTypeMap[$subType];
    }

    public function nameFile(string $name): string
    {
        $parts = explode('.', $name);

        return $parts[0];
    }

    public function formatSize(float $size, $type = true): string
    {
        if ($size >= 1024 ** 3) {
            return number_format($size / (1024 ** 3), 2) . ($type ? ' Go' : '');
        }
        if ($size >= 1024 ** 2) {
            return number_format($size / (1024 ** 2), 2) . ($type ? ' Mo' : '');
        }
        if ($size >= 1024) {
            return number_format($size / 1024, 2) . ($type ? ' Ko' : '');
        }

        return number_format($size, 2) . ($type ? ' octets' : '');
    }

    public function typeFile(string $mimeType): string
    {
        $mimeTypeMap = [
            'application/pdf' => 'Document',
            'application/x-pdf' => 'Document',
            'image/png' => 'Image',
            'image/jpeg' => 'Image',
            'image/jpg' => 'Image',
            'application/msword' => 'Document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Document',
            'application/vnd.ms-excel' => 'Tableur',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Tableur',
            'application/vnd.ms-powerpoint' => 'Présentation',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'Présentation',
            'text/plain' => 'Texte Brut',
            'text/csv' => 'Fichier',
            'application/zip' => 'Archive',
            'application/x-rar-compressed' => 'Archive',
            'application/x-7z-compressed' => 'Archive',
            'application/x-tar' => 'Archive',
            'audio/mpeg' => 'Audio',
            'audio/wav' => 'Audio',
            'video/mp4' => 'Vidéo',
            'video/x-msvideo' => 'Vidéo',
        ];

        return $mimeTypeMap[$mimeType] ?? 'Type de fichier inconnu';
    }
}
