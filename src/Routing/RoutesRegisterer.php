<?php

namespace Ludens\Routing;

use Ludens\Exceptions\InvalidControllerFolderException;
use Ludens\Routing\Support\Handler;
use Ludens\Routing\Support\MethodAttribute;

final class RoutesRegisterer
{
    public function __construct(
        private MethodAttributesResolver $methodAttributesResolver,
        private readonly string $controllerFolder,
        private readonly string $controllerNamespace
    )
    {}

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

    private function retrieveControllersFiles(): array
    {
        if (false === is_dir($this->controllerFolder)) {
            throw new InvalidControllerFolderException(
                "The controller folder {$this->controllerFolder} does not exist"
            );
        }
        return glob("{$this->controllerFolder}*.php");
    }

    private function formatClassName(string $file): string
    {
        $classname = str_replace($this->controllerFolder, '', $file);
        $classname = str_replace('.php', '', $classname);
        return "{$this->controllerNamespace}{$classname}";
    }
}
