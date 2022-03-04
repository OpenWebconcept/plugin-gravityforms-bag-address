<?php

namespace Yard\BAG\Foundation;

use Exception;

class Plugin
{
    /**
     * Name of the plugin.
     *
     * @var string
     */
    const NAME = GF_BAG_SLUG;

    /**
     * Version of the plugin.
     *
     * @var string
     */
    const VERSION = GF_BAG_VERSION;

    /**
     * Path to the root of the plugin.
     *
     * @var string
     */
    protected $rootPath;

    /**
     * Instance of the configuration repository.
     *
     * @var \Yard\BAG\Foundation\Config
     */
    public $config;

    /**
     * Instance of the hook loader.
     *
     * @var \Yard\BAG\Foundation\Loader
     */
    public $loader;

    /**
     * Constructs the plugin.
     *
     * Create startup hooks and tear down hooks.
     * Boot up admin and frontend functionality.
     * Register the actions and filters from the loader.
     *
     * @param string $rootPath
     */
    public function __construct($rootPath = '')
    {
        $this->rootPath = $rootPath;
        load_plugin_textdomain($this->getName(), false, basename(GF_BAG_ROOT_PATH) . '/languages');
    }

    /**
     * Boot the plugin.
     *
     * @return void
     */
    public function boot(): void
    {
        require_once __DIR__ .'/Helpers.php';

        $this->config = new Config($this->rootPath.'/config');
        $this->config->boot();

        $this->loader = Loader::getInstance();

        $this->bootServiceProviders();

        $this->loader->addAction('wp_enqueue_scripts', $this, 'enqueueScripts');
        $this->loader->register();
    }

    /**
     * Enqueue scripts within WordPress.
     *
     * @return void
     */
    public function enqueueScripts(): void
    {
        \wp_enqueue_style(GF_BAG_SLUG, $this->resourceUrl(GF_BAG_SLUG .'.css', 'css'), [], GF_BAG_VERSION);
    }

    /**
     * Get the name of the plugin.
     *
     * @return string
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * Get the version of the plugin.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return static::VERSION;
    }

    /**
     * Get the root path of the plugin.
     *
     * @return string
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * Get the path to a particular resource.
     *
     * @var string $file
     * @var string $directory
     *
     * @return string
     */
    public function resourceUrl(string $file, string $directory = ''): string
    {
        $directory = !empty($directory) ? $directory .'/' : '';
        return plugins_url("resources/{$directory}/{$file}", GF_BAG_SLUG .'/plugin.php');
    }

    /**
     * Boot service providers.
     *
     * @return void
     */
    protected function bootServiceProviders(): void
    {
        $services = $this->config->get('core.providers');

        foreach ($services as $service) {
            // Only boot global service providers here.
            if (is_array($service)) {
                continue;
            }

            $service = new $service($this);

            if (!$service instanceof ServiceProvider) {
                throw new Exception('Provider must extend ServiceProvider.');
            }

            $service->register();
        }
    }
}
