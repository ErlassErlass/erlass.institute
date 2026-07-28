<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload file with standardized path structure.
     * Path: uploads/{category}/{year}/{month}/{filename}
     *
     * @param UploadedFile $file The file to upload
     * @param string $category The category folder (e.g. 'instructors', 'reports')
     * @param string|null $subfolder Optional subfolder for ID or grouping
     * @return string The stored file path relative to public disk
     */
    public function upload(UploadedFile $file, string $category, ?string $subfolder = null, int $maxDimension = 1600, int $quality = 80): string
    {
        $year = date('Y');
        $month = date('m');
        
        // Base path: uploads/category
        $path = "uploads/{$category}";
        
        // Add subfolder if exists: uploads/category/subfolder
        if ($subfolder) {
            $path .= "/{$subfolder}";
        }
        
        // Add date structure: uploads/category/subfolder/2025/02
        $path .= "/{$year}/{$month}";
        
        // Generate secure filename with guessed extension to prevent extension spoofing
        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $forbiddenExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'phps', 'phar', 'exe', 'sh', 'pl', 'cgi', 'js', 'html', 'htm', 'bat', 'cmd'];
        
        if (in_array($extension, $forbiddenExtensions, true)) {
            throw new \InvalidArgumentException('Tipe file tidak diizinkan demi keamanan.');
        }

        $filename = Str::random(40) . '.' . $extension;
        
        // Store
        $storedPath = $file->storeAs($path, $filename, 'public');

        // Automatically compress & optimize image files (JPG/PNG/WEBP)
        $fullPath = storage_path('app/public/' . $storedPath);
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true) && extension_loaded('gd') && file_exists($fullPath)) {
            $this->optimizeImage($fullPath, $extension, $maxDimension, $quality);
        }

        return $storedPath;
    }

    /**
     * Compress and resize image file in-place using PHP GD.
     */
    public function optimizeImage(string $filePath, string $extension, int $maxDimension = 1600, int $quality = 80): bool
    {
        try {
            list($width, $height) = @getimagesize($filePath);
            if (!$width || !$height) return false;

            // Compute scaled dimensions
            if ($width > $maxDimension || $height > $maxDimension) {
                $ratio = min($maxDimension / $width, $maxDimension / $height);
                $newWidth = (int)round($width * $ratio);
                $newHeight = (int)round($height * $ratio);
            } else {
                $newWidth = $width;
                $newHeight = $height;
            }

            // Create GD image resource based on extension
            $srcImg = match ($extension) {
                'jpg', 'jpeg' => @imagecreatefromjpeg($filePath),
                'png' => @imagecreatefrompng($filePath),
                'webp' => @imagecreatefromwebp($filePath),
                default => null
            };

            if (!$srcImg) return false;

            $dstImg = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve alpha channel for PNG/WEBP
            if (in_array($extension, ['png', 'webp'], true)) {
                imagealphablending($dstImg, false);
                imagesavealpha($dstImg, true);
            }

            imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // Save back to file with compression
            match ($extension) {
                'jpg', 'jpeg' => imagejpeg($dstImg, $filePath, $quality),
                'png' => imagepng($dstImg, $filePath, (int)round((100 - $quality) / 10)),
                'webp' => imagewebp($dstImg, $filePath, $quality),
                default => null
            };

            imagedestroy($srcImg);
            imagedestroy($dstImg);
            return true;
        } catch (\Exception $e) {
            \Log::error('Image optimization failed for ' . $filePath . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete file from storage.
     *
     * @param string|null $path
     * @return bool
     */
    public function delete(?string $path): bool
    {
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }
}
