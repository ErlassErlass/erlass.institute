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
    public function upload(UploadedFile $file, string $category, ?string $subfolder = null): string
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
        return $file->storeAs($path, $filename, 'public');
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
