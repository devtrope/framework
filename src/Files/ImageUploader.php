<?php

namespace Ludens\Files;

use Exception;
use Ludens\Core\Application;

/**
 * Handle secure image uploads with validation.
 * 
 * @package Ludens\Files
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class ImageUploader
{
    private string $uploadDirectory;

    /**
     * @param string $uploadDirectory Physical path where files are stored
     */
    public function __construct(
        string $uploadDirectory
    ) {
        $this->uploadDirectory = $uploadDirectory;
        $this->ensureDirectoryExists();
    }

    public function upload(array $file): string
    {
        $noErrorFile = FileError::OK;

        if ($file['error'] !== $noErrorFile->value) {
            throw new Exception(
                FileError::fromCode($file['error'])->message()
            );
        }

        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = uniqid() . '.' . $fileExtension;
        $targetPath = Application::getInstance()->config('public') . $this->uploadDirectory . $fileName;

        if (! move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("An error occured during the upload of the file.");
        }

        return $this->uploadDirectory . $fileName;
    }

    /**
     * Ensure upload directory exists and is writable.
     *
     * @return void
     *
     * @throws Exception
     */
    private function ensureDirectoryExists()
    {
        $completeUploadDirectory = Application::getInstance()->path('public') . $this->uploadDirectory;

        if (! is_dir($completeUploadDirectory)) {
            throw new Exception(
                "The {$completeUploadDirectory} directory does not exist."
            );
        }

        if (! is_writable($completeUploadDirectory)) {
            throw new Exception(
                "The {$completeUploadDirectory} directory is not writable."
            );
        }
    }
}
