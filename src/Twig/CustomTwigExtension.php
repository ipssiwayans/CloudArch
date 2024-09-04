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
            new TwigFilter('type_file', [$this, 'typeFile']),
            new TwigFilter('name_file', [$this, 'nameFile']),
            new TwigFilter('format_size', [$this, 'formatSize']),
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
        $parts = explode('/', $mimeType);

        return end($parts);
    }

    public function typeFile(string $mimeType): string
    {
        $parts = explode('/', $mimeType);

        return $parts[0];
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
}
