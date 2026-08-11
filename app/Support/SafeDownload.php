<?php

namespace App\Support;

class SafeDownload
{
    public static function path(string $directory, string $file, array $allowedPrefixes = []): string
    {
        $file = basename(str_replace('\\', '/', $file));

        if ($file === '' || $file === '.' || str_contains($file, '..')) {
            abort(404);
        }

        if ($allowedPrefixes !== []) {
            $allowed = false;
            foreach ($allowedPrefixes as $prefix) {
                if (str_starts_with($file, $prefix) || $file === $prefix) {
                    $allowed = true;
                    break;
                }
            }
            if (! $allowed) {
                abort(404);
            }
        }

        $directory = rtrim($directory, DIRECTORY_SEPARATOR);
        $path = $directory.DIRECTORY_SEPARATOR.$file;

        if (! is_file($path)) {
            abort(404);
        }

        $realFile = realpath($path);
        $realDir = realpath($directory);

        if ($realFile === false || $realDir === false || ! str_starts_with($realFile, $realDir.DIRECTORY_SEPARATOR)) {
            abort(404);
        }

        return $realFile;
    }
}
