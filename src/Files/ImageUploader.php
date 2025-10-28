<?php

namespace Ludens\Files;

use Exception;

class ImageUploader
{
    private string $uploadDirectory;

    public function __construct(string $uploadDirectory = '/assets/images/')
    {
        $this->uploadDirectory = rtrim($uploadDirectory, '/') . '/';
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
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . $this->uploadDirectory . $fileName;
        
        if (! move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception("An error occured during the upload of the file.");
        }

        return $this->uploadDirectory . $fileName;
    }
}
