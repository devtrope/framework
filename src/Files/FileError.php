<?php

namespace Ludens\Files;

enum FileError: int
{
    case OK         = 0;
    case INI_SIZE   = 1;
    case FORM_SIZE  = 2;
    case PARTIAL    = 3;
    case NO_FILE    = 4;
    case NO_TMP_DIR = 6;
    case CANT_WRITE = 7;
    case EXTENSION  = 8;
    
    public function message(): string
    {
        return match($this) {
            self::OK         => 'There is no error, the file uploaded successfully.',
            self::INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            self::FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
            self::PARTIAL    => 'The uploaded file was only partially uploaded.',
            self::NO_FILE    => 'No file was uploaded.',
            self::NO_TMP_DIR => 'Missing a temporary folder.',
            self::CANT_WRITE => 'Failed to write file to disk.',
            self::EXTENSION  => 'A PHP extension stopped the file upload.',
        };
    }
    
    public static function fromCode(int $code): self
    {
        if (! self::tryFrom($code)) {
            throw new \InvalidArgumentException("Unknown uploaded error code: {$code}");
        }
        return self::tryFrom($code);
    }
}
