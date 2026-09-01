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
    /**
     * Upload file with standardized path structure and optional geotag watermarking.
     * Path: uploads/{category}/{year}/{month}/{filename}
     *
     * @param UploadedFile $file The file to upload
     * @param string $category The category folder (e.g. 'instructors', 'reports', 'checkin_photos')
     * @param string|null $subfolder Optional subfolder for ID or grouping
     * @param int $maxDimension Max width/height for compression
     * @param int $quality JPEG/WEBP quality (0-100)
     * @param array|null $watermarkData Optional metadata for burned-in geotag watermark ['school', 'meeting', 'time', 'coords']
     * @return string The stored file path relative to public disk
     */
    public function upload(UploadedFile $file, string $category, ?string $subfolder = null, int $maxDimension = 1600, int $quality = 80, ?array $watermarkData = null): string
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

            // Apply burned-in geotag watermark if metadata is provided
            if ($watermarkData) {
                $this->addGeotagWatermark($fullPath, $watermarkData);
            }
        }

        return $storedPath;
    }

    /**
     * Upload image from Base64 Data URL or raw base64 string.
     * Prevents ERR_UPLOAD_FILE_CHANGED on Android mobile browsers.
     */
    public function uploadBase64(string $base64Data, string $category, ?string $subfolder = null, int $maxDimension = 1600, int $quality = 80, ?array $watermarkData = null): ?string
    {
        if (empty($base64Data)) return null;

        $extension = 'jpg';
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            $extension = strtolower($type[1]);
            if ($extension === 'jpeg') $extension = 'jpg';
        }

        $decodedData = base64_decode($base64Data);
        if ($decodedData === false) return null;

        $year = date('Y');
        $month = date('m');
        $path = "uploads/{$category}";
        if ($subfolder) {
            $path .= "/{$subfolder}";
        }
        $path .= "/{$year}/{$month}";

        $filename = Str::random(40) . '.' . $extension;
        $storedPath = "{$path}/{$filename}";

        Storage::disk('public')->put($storedPath, $decodedData);

        $fullPath = storage_path('app/public/' . $storedPath);
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true) && extension_loaded('gd') && file_exists($fullPath)) {
            $this->optimizeImage($fullPath, $extension, $maxDimension, $quality);

            if ($watermarkData) {
                $this->addGeotagWatermark($fullPath, $watermarkData);
            }
        }

        return $storedPath;
    }

    /**
     * Alias for addGeotagWatermark for backward compatibility.
     */
    public function applyGeotagWatermark(string $filePath, array $data): bool
    {
        return $this->addGeotagWatermark($filePath, $data);
    }

    /**
     * Burn-in a sleek geotag and timestamp watermark onto the image file using PHP GD.
     */
    public function addGeotagWatermark(string $filePath, array $data): bool
    {
        try {
            if (!file_exists($filePath) || !extension_loaded('gd')) {
                return false;
            }

            list($width, $height, $type) = @getimagesize($filePath);
            if (!$width || !$height) return false;

            $img = match($type) {
                IMAGETYPE_JPEG => @imagecreatefromjpeg($filePath),
                IMAGETYPE_PNG  => @imagecreatefrompng($filePath),
                IMAGETYPE_WEBP => @imagecreatefromwebp($filePath),
                default => null
            };

            if (!$img) return false;

            $bannerHeight = max(75, (int)round($height * 0.13));
            $bannerTop = $height - $bannerHeight;

            imagealphablending($img, true);

            // Draw dark gradient overlay at the bottom
            for ($i = 0; $i < $bannerHeight; $i++) {
                $y = $bannerTop + $i;
                $progress = $i / $bannerHeight;
                if ($progress < 0.25) {
                    $alpha = (int)round(127 - ($progress / 0.25) * (127 - 40));
                } else {
                    $alpha = (int)round(40 - (($progress - 0.25) / 0.75) * (40 - 15));
                }
                $black = imagecolorallocatealpha($img, 0, 0, 0, $alpha);
                imageline($img, 0, $y, $width, $y, $black);
            }

            $fontBold = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
            $fontRegular = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';

            $fontSize1 = max(11, (int)round($width * 0.024));
            $fontSize2 = max(9, (int)round($fontSize1 * 0.85));

            $white = imagecolorallocate($img, 255, 255, 255);
            $lightGray = imagecolorallocate($img, 240, 240, 240);
            $shadowColor = imagecolorallocatealpha($img, 0, 0, 0, 40);

            $schoolName = $data['school'] ?? 'Erlass Institute';
            $meetingNo = $data['meeting'] ?? '?';
            $timeString = $data['time'] ?? now()->timezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB';
            $coordsString = $data['coords'] ?? 'GPS Aktif';

            $line1 = "LOKASI: {$schoolName} • Pertemuan {$meetingNo}";
            $line2 = "WAKTU: {$timeString} • GPS: {$coordsString}";

            $padX = 18;
            $y1 = $bannerTop + (int)round($bannerHeight * 0.45);
            $y2 = $bannerTop + (int)round($bannerHeight * 0.82);

            if (file_exists($fontBold) && function_exists('imagettftext')) {
                // Shadow Line 1
                imagettftext($img, $fontSize1, 0, $padX + 1, $y1 + 1, $shadowColor, $fontBold, $line1);
                // Text Line 1
                imagettftext($img, $fontSize1, 0, $padX, $y1, $white, $fontBold, $line1);

                // Shadow Line 2
                imagettftext($img, $fontSize2, 0, $padX + 1, $y2 + 1, $shadowColor, $fontRegular, $line2);
                // Text Line 2
                imagettftext($img, $fontSize2, 0, $padX, $y2, $lightGray, $fontRegular, $line2);
            } else {
                imagestring($img, 4, $padX, $bannerTop + 12, $line1, $white);
                imagestring($img, 3, $padX, $bannerTop + 38, $line2, $lightGray);
            }

            match($type) {
                IMAGETYPE_JPEG => imagejpeg($img, $filePath, 85),
                IMAGETYPE_PNG  => imagepng($img, $filePath, 6),
                IMAGETYPE_WEBP => imagewebp($img, $filePath, 85),
                default => null
            };

            imagedestroy($img);
            return true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Geotag watermarking failed: ' . $e->getMessage());
            return false;
        }
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
