<?php

namespace Yard\BAG\Foundation\Helpers;

/**
 * @param string $setting
 * @param string $default
 *
 * @return string
 */
function config(string $setting, string $default = ''): ?string
{
    $config   = new \Yard\BAG\Foundation\Config(GF_BAG_ROOT_PATH.'/config');
    $config->boot();
    return $config->get($setting, $default);
}

/**
 * Dump the passed variables and end the script.
 *
 * @param  array|string|int  ...$args
 * @return void
 */
function dd(...$args): void
{
    echo '<pre>';
    foreach ($args as $x) {
        var_dump($x);
    }
    echo '</pre>';
    die(1);
}
