<?php

namespace Ludens\Routing;

use Ludens\Exceptions\InvalidControllerFolderException;
use Ludens\Routing\Support\Handler;
use Ludens\Routing\Support\MethodAttribute;

final class RoutesRegisterer
{
    /**
     * @param MethodAttributesResolver $methodAttributesResolver
     * @param string $controllerFolder
     * @param string $controllerNamespace
     */
    public function __construct(
        private MethodAttributesResolver $methodAttributesResolver,
        private readonly string $controllerFolder,
        private readonly string $controllerNamespace
    )
    {}

    /**
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
     * @throws InvalidControllerFolderException
     * @return string[]
     */
    private function retrieveControllersFiles(): array
    {
        if (false === is_dir($this->controllerFolder)) {
            throw new InvalidControllerFolderException(\sprintf(
                'The controller folder %s does not exist',
                $this->controllerFolder
            ));
        }

        if (false === $files = glob("{$this->controllerFolder}*.php")) {
            throw new InvalidControllerFolderException(\sprintf(
                'Cannot access %s directory',
                $this->controllerFolder
            ));
        }
        return $files;
    }

    /**
     * @param string $file
     * @return string
     */
    private function formatClassName(string $file): string
    {
        $classname = str_replace($this->controllerFolder, '', $file);
        $classname = str_replace('.php', '', $classname);
        return "{$this->controllerNamespace}{$classname}";
    }
}
