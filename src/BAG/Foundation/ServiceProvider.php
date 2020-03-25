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

    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Return Foundation plugin.
     *
     * @return \Yard\BAG\Foundation\Plugin
     */
    public function plugin(): \Yard\BAG\Foundation\Plugin
    {
        return $this->plugin;
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    abstract public function register(): void;
}
