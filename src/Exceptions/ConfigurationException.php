<?php

namespace Ludens\Exceptions;

/**
 * Exception throws when configuration is invalid or missing.
 */
class ConfigurationException extends \RuntimeException
{
    public static function pathNotAnalyzable(string $path): self
    {
        return new self(
            "Unable to read configuration files from path: {$path}. " .
            "Ensure the directory exists and contains PHP configuration files."
        );
    }

    public static function invalidProvidersFormat(): self
    {
        return new self(
            "The 'providers' configuration must be an array of service provider class names. " .
            "Check your config/providers.php file."
        );
    }

    public static function missingUploadDirectory(): self
    {
        return new self(
            "Upload directory not configured. " .
            "Please set 'filesystems.images.root' in config/filesystems.php"
        );
    }

    public static function missingAppUrl(): self
    {
        return new self(
            "Application URL not configured. " .
            "Please set 'app.url' in config/app.php or APP_URL in .env"
        );
    }

    public static function invalidValue(string $key, string $expectedType, mixed $actualValue): self
    {
        $actualType = get_debug_type($actualValue);

        return new self(
            "Configuration value for '{$key}' must be of type {$expectedType}, {$actualType} given."
        );
    }
}
