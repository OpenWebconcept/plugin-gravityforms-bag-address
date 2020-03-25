<?php

namespace Yard\BAG\Foundation;

abstract class ServiceProvider
{

    /**
     * Instance of the plugin.
     *
     * @var \Yard\BAG\Foundation\Plugin
     */
    protected $plugin;

    public function __construct(Plugin $plugin = null)
    {
        $this->plugin = $plugin;
    }

    public function plugin()
    {
        return $this->plugin;
    }

    /**
     * Register the service provider.
     */
    abstract public function register();
}
