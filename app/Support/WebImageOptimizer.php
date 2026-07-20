<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Optimiza imágenes para uso web (resize + JPEG).
 */
final class WebImageOptimizer
{
    public static function optimizeFile(
        string $absolutePath,
        int $maxEdge = 1600,
        int $quality = 80,
    ): bool {
        if (! is_file($absolutePath) || ! self::magickAvailable()) {
            return false;
        }

        $originalSize = filesize($absolutePath) ?: 0;
        if ($originalSize <= 0) {
            return false;
        }

        $tmp = $absolutePath.'.opt.'.Str::random(6).'.jpg';
        $cmd = sprintf(
            'magick %s -auto-orient -strip -resize %s -quality %d -interlace Plane %s 2>/dev/null',
            escapeshellarg($absolutePath),
            escapeshellarg($maxEdge.'x'.$maxEdge.'>'),
            $quality,
            escapeshellarg($tmp)
        );

        exec($cmd, $output, $code);
        if ($code !== 0 || ! is_file($tmp)) {
            @unlink($tmp);

            return false;
        }

        $newSize = filesize($tmp) ?: PHP_INT_MAX;
        if ($newSize >= $originalSize && str_ends_with(strtolower($absolutePath), '.jpg')) {
            // Ya es JPEG y no mejoró: mantener original.
            @unlink($tmp);

            return false;
        }

        // Si el destino no es .jpg, reemplazamos el contenido solo si el path es .jpg;
        // para .png el caller debe manejar conversión de path.
        if (preg_match('/\.(jpe?g)$/i', $absolutePath)) {
            rename($tmp, $absolutePath);

            return true;
        }

        @unlink($tmp);

        return false;
    }

    /**
     * Guarda un upload optimizado como JPEG en el disco público.
     *
     * @return string Path relativo en el disco (ej. pathologies/xxx.jpg)
     */
    public static function storeOptimized(
        UploadedFile $file,
        string $directory,
        int $maxEdge = 1600,
        int $quality = 80,
        string $disk = 'public',
    ): string {
        $filename = Str::random(40).'.jpg';
        $relative = trim($directory, '/').'/'.$filename;
        $absolute = Storage::disk($disk)->path($relative);

        File::ensureDirectoryExists(dirname($absolute));

        if (self::magickAvailable()) {
            $tmpUpload = $file->getRealPath();
            $cmd = sprintf(
                'magick %s -auto-orient -strip -resize %s -quality %d -interlace Plane %s 2>/dev/null',
                escapeshellarg((string) $tmpUpload),
                escapeshellarg($maxEdge.'x'.$maxEdge.'>'),
                $quality,
                escapeshellarg($absolute)
            );
            exec($cmd, $output, $code);
            if ($code === 0 && is_file($absolute) && filesize($absolute) > 0) {
                return $relative;
            }
            @unlink($absolute);
        }

        // Fallback sin ImageMagick: guardar original.
        return $file->storeAs($directory, $filename, $disk);
    }

    /**
     * Optimiza un archivo ya guardado. Si era PNG/WebP grande, convierte a JPEG
     * y devuelve el nuevo path relativo (o el mismo si no cambió).
     */
    public static function optimizeStored(
        string $relativePath,
        int $maxEdge = 1600,
        int $quality = 80,
        int $minBytesToTouch = 180_000,
        string $disk = 'public',
    ): string {
        $storage = Storage::disk($disk);
        if (! $storage->exists($relativePath)) {
            return $relativePath;
        }

        $absolute = $storage->path($relativePath);
        $size = filesize($absolute) ?: 0;
        if ($size < $minBytesToTouch) {
            return $relativePath;
        }

        $ext = strtolower(pathinfo($absolute, PATHINFO_EXTENSION));
        $isJpeg = in_array($ext, ['jpg', 'jpeg'], true);

        if ($isJpeg) {
            self::optimizeFile($absolute, $maxEdge, $quality);

            return $relativePath;
        }

        if (! self::magickAvailable()) {
            return $relativePath;
        }

        $newRelative = preg_replace('/\.[^.]+$/', '.jpg', $relativePath) ?: ($relativePath.'.jpg');
        if ($newRelative === $relativePath) {
            $newRelative = $relativePath.'.jpg';
        }
        $newAbsolute = $storage->path($newRelative);

        $cmd = sprintf(
            'magick %s -auto-orient -strip -resize %s -quality %d -interlace Plane %s 2>/dev/null',
            escapeshellarg($absolute),
            escapeshellarg($maxEdge.'x'.$maxEdge.'>'),
            $quality,
            escapeshellarg($newAbsolute)
        );
        exec($cmd, $output, $code);

        if ($code !== 0 || ! is_file($newAbsolute) || (filesize($newAbsolute) ?: 0) <= 0) {
            @unlink($newAbsolute);

            return $relativePath;
        }

        if ($newRelative !== $relativePath) {
            $storage->delete($relativePath);
        }

        return $newRelative;
    }

    private static function magickAvailable(): bool
    {
        static $available = null;
        if ($available !== null) {
            return $available;
        }

        exec('command -v magick 2>/dev/null', $out, $code);
        $available = $code === 0;

        return $available;
    }
}
