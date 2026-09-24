<?php

namespace Ludens\Core;

use Ludens\DependencyInjection\Container;
use Ludens\Exceptions\ConfigurationException;
use Ludens\Exceptions\RouteNotFoundException;
use Ludens\Http\Request;
use Ludens\Http\Response;
use Ludens\Http\Support\HttpResponseCode;
use Ludens\Routing\Router;
use Ludens\Routing\RoutesRegisterer;
use Ludens\Sphp\Sphp;

final class Kernel
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $configuration = [];

    /**
     * @var Kernel|null
     */
    private static ?Kernel $instance = null;
    
    /**
     * @param Container $container
     * @param Sphp $sphp
     */
    private function __construct(
        private Container $container = new Container(),
        private Sphp $sphp = new Sphp()
    )
    {}

    /**
     * @return Kernel|null
     */
    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new Kernel();
        }
        return self::$instance;
    }

    /**
     * @param string $projectDirectory
     * @return Kernel
     */
    public function loadProjectDirectory(string $projectDirectory): self
    {
        $this->configuration['kernel']['projectDirectory'] = $projectDirectory . '/';
        return $this;
    }

    /**
     * @param string $configurationDirectory
     * @throws ConfigurationException
     * @return Kernel
     */
    public function loadConfiguration(string $configurationDirectory): self
    {
        if (false === is_dir($configurationDirectory)) {
            throw new ConfigurationException(\sprintf(
                'The configuration directory %s does not exist',
                $configurationDirectory
            ));
        }
        
        if (false === $configurationFiles = glob("{$configurationDirectory}*.sphp")) {
            throw new ConfigurationException(\sprintf(
                'Cannot access %s directory',
                $configurationDirectory
            ));
        }

        foreach ($configurationFiles as $file) {
            $configuration = $this->sphp->parse($file);
            $keys = array_keys($configuration);
            foreach ($keys as $key) {
                $this->configuration[$key] = $configuration[$key];
            }
        }

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        [$key, $value] = explode('.', $key);
        $value = ucwords($value, '_');
        $value = str_replace('_', '', lcfirst($value));
        return $this->configuration[$key][$value];
    }

    /**
     * @param Request $request
     * @return void
     */
    public function run(Request $request): void
    {
        $this->container->load(dirname(__DIR__) . '/Routing/Configuration/services.sphp');
        $registerer = $this->container->get(RoutesRegisterer::class);
        $registerer->register();

        try {
            $response = Router::run($request);
            $response->send();
        } catch (RouteNotFoundException $exception) {
            $response = new Response();
            $response
                ->setBody($exception->getMessage())
                ->setCode(HttpResponseCode::NOT_FOUND)
                ->send();
        }
    }
}
