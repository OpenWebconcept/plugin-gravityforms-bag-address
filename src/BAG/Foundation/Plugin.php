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
    const NAME = 'owc-gravityforms-bag-address';

    /**
     * Version of the plugin.
     *
     * @var string
     */
    const VERSION = '0.0.1';

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
        load_plugin_textdomain($this->getName(), false, $this->getName() . '/languages/');
    }

    /**
     * Boot the plugin.
     */
    public function boot()
    {
        require_once __DIR__ .'/helpers.php';

        $this->config = new Config($this->rootPath.'/config');
        $this->config->boot();

        $this->loader = Loader::getInstance();

        $this->bootServiceProviders();

        $this->loader->addAction('wp_enqueue_scripts', $this, 'enqueueScripts');
        $this->loader->register();
    }

    /**
     * Enqueue scripts within WordPress.
     */
    public function enqueueScripts()
    {
        wp_enqueue_style(GF_B_A_ROOT_PATH, $this->resourceUrl(GF_B_A_PLUGIN_SLUG .'.css', 'css'), false);
    }

    /**
     * Get the name of the plugin.
     *
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * Get the version of the plugin.
     *
     * @return string
     */
    public function getVersion()
    {
        return static::VERSION;
    }

    /**
     * Get the root path of the plugin.
     *
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * Get the path to a particular resource.
     *
     * @var string $file
     *
     * @return string
     */
    public function resourceUrl($file, $directory = '')
    {
        $directory = !empty($directory) ? $directory .'/' : '';
        return plugins_url("resources/{$directory}/{$file}", GF_B_A_PLUGIN_SLUG .'/plugin.php');
    }

    /**
     * Boot service providers.
     */
    protected function bootServiceProviders()
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
