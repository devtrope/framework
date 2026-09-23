<?php

namespace Ludens\Routing;

use Ludens\Exceptions\InvalidControllerFolderException;
use Ludens\Routing\Support\Handler;
use Ludens\Routing\Support\MethodAttribute;

final class RoutesRegisterer
{
    /**
     * @param MethodAttributesResolver $methodAttributesResolver
     * @param string $controllerDirectory
     * @param string $controllerNamespace
     */
    public function __construct(
        private MethodAttributesResolver $methodAttributesResolver,
        private readonly string $controllerDirectory,
        private readonly string $controllerNamespace
    )
    {}

    /**
     * Register the routes retrieved from the controllers with the right attributes linked to the methods (GET, POST, PUT, PATCH, DELETE).
     *
     * @return void
     */
    public function register(): void
    {
        foreach ($this->retrieveControllersFiles() as $file) {
            $attributes = $this->methodAttributesResolver->getAllByClassName($this->formatClassName($file));
            /**
             * @var MethodAttribute $attribute
             */
            foreach ($attributes as $attribute) {
                $handler = new Handler($attribute->getClassName(), $attribute->getMethod());
                Route::add($attribute->getInstance()->getHttpMethod(), $attribute->getInstance()->getPath(), $handler);
            }
        }
    }

    /**
     * Return the files from the controller directory provided in the services configuration file and
     * ensure the directory is valid and readable.
     *
     * @throws InvalidControllerFolderException
     * @return string[]
     */
    private function retrieveControllersFiles(): array
    {
        if (false === is_dir($this->controllerDirectory)) {
            throw new InvalidControllerFolderException(\sprintf(
                'The controller directory %s does not exist',
                $this->controllerDirectory
            ));
        }

        if (false === $files = glob("{$this->controllerDirectory}*.php")) {
            throw new InvalidControllerFolderException(\sprintf(
                'Cannot access %s directory',
                $this->controllerDirectory
            ));
        }
        return $files;
    }

    /**
     * Remove the directory informations from the controller class name and replace it with the
     * controller namespace to make it readable for the Reflection classes.
     *
     * @param string $file
     * @return string
     */
    private function formatClassName(string $file): string
    {
        $classname = str_replace($this->controllerDirectory, '', $file);
        $classname = str_replace('.php', '', $classname);
        return "{$this->controllerNamespace}{$classname}";
    }
}
