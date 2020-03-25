<?php

namespace Yard\BAG\Foundation;

abstract class SettingsManager
{
    /**
    * Key of the option.
    *
    * @var string
    */
    protected $key = '';

    /**
    * Setting array of the option.
    */
    protected $settings;

    public function __construct($key = '')
    {
        if (! empty($key)) {
            $this->key     = $key;
        }
    }

    /**
     * Static constructor for quick setup.
     *
     * @return self
     */
    public static function make($key = ''): self
    {
        $class = get_called_class();
        return new $class($key);
    }

    /**
     * Save the data to the database.
    *
    * @param array $data
    *
    * @return boolean
    */
    public function save(array $data): bool
    {
        return update_option($this->key, $data);
    }

    /**
     * Get the attributes.
     *
     * @param array $default
     *
     * @return array[]
     */
    public function all($default = [])
    {
        $all = get_option($this->key, $default);

        return $all;
    }

    public function get($key, $default = [])
    {
        $all = $this->all($default);

        return $all[$key] ?? $all;
    }

    /**
     * Find a specific value by key.
     *
     * @param string $key
     * @param array $default
     *
     * @return string|array
     */
    public function find($key, $default = [])
    {
        return $this->get($key, $default);
    }
}
