<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Secure File Upload Validation Rule
 *
 * Validates uploaded files by checking:
 * - Real MIME type (not just extension)
 * - File size limits
 * - Dangerous file patterns
 * - Magic bytes verification
 */
class SecureFileUpload implements ValidationRule
{
    /**
     * Allowed MIME types by category
     */
    private const ALLOWED_MIMES = [
        'images' => [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
        ],
        'documents' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
        ],
        'archives' => [
            'application/zip',
            'application/x-zip-compressed',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
        ],
    ];

    /**
     * Dangerous file extensions that should never be uploaded
     */
    private const DANGEROUS_EXTENSIONS = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps',
        'exe', 'com', 'bat', 'cmd', 'scr', 'vbs', 'js', 'jar',
        'sh', 'bash', 'csh', 'ksh', 'pl', 'py', 'rb',
        'asp', 'aspx', 'jsp', 'jspx',
    ];

    /**
     * Create a new rule instance.
     *
     * @param array<string> $allowedCategories Categories of files to allow (images, documents, archives)
     * @param int $maxSizeMB Maximum file size in megabytes
     */
    public function __construct(
        private array $allowedCategories = ['images', 'documents'],
        private int $maxSizeMB = 10
    ) {
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile) {
            $fail('The :attribute must be a valid uploaded file.');
            return;
        }

        // Check if file was uploaded successfully
        if (!$value->isValid()) {
            $fail('The :attribute upload failed. Please try again.');
            return;
        }

        // Check file size
        $maxSizeBytes = $this->maxSizeMB * 1024 * 1024;
        if ($value->getSize() > $maxSizeBytes) {
            $fail("The :attribute must not exceed {$this->maxSizeMB}MB.");
            return;
        }

        // Check for dangerous extensions
        $extension = strtolower($value->getClientOriginalExtension());
        if (in_array($extension, self::DANGEROUS_EXTENSIONS, true)) {
            $fail('The :attribute has a forbidden file type.');
            return;
        }

        // Get real MIME type from file content (not from extension)
        $realMimeType = $value->getMimeType();

        // Build allowed MIME types from categories
        $allowedMimeTypes = [];
        foreach ($this->allowedCategories as $category) {
            if (isset(self::ALLOWED_MIMES[$category])) {
                $allowedMimeTypes = array_merge(
                    $allowedMimeTypes,
                    self::ALLOWED_MIMES[$category]
                );
            }
        }

        // Validate MIME type
        if (!in_array($realMimeType, $allowedMimeTypes, true)) {
            $fail('The :attribute must be a valid file type.');
            return;
        }

        // Additional security check: verify magic bytes for common formats
        if (!$this->verifyMagicBytes($value, $realMimeType)) {
            $fail('The :attribute file appears to be corrupted or invalid.');
            return;
        }
    }

    /**
     * Verify file magic bytes match the declared MIME type
     */
    private function verifyMagicBytes(UploadedFile $file, string $mimeType): bool
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if ($handle === false) {
            return false;
        }

        $bytes = fread($handle, 8);
        fclose($handle);

        if ($bytes === false) {
            return false;
        }

        // Check magic bytes for common file types
        return match ($mimeType) {
            'image/jpeg', 'image/jpg' => str_starts_with($bytes, "\xFF\xD8\xFF"),
            'image/png' => str_starts_with($bytes, "\x89\x50\x4E\x47"),
            'image/gif' => str_starts_with($bytes, 'GIF87a') || str_starts_with($bytes, 'GIF89a'),
            'application/pdf' => str_starts_with($bytes, '%PDF'),
            'application/zip', 'application/x-zip-compressed' => str_starts_with($bytes, 'PK'),
            // For other types, we trust the MIME detection
            default => true,
        };
    }
}
