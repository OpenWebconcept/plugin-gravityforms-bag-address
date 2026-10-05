<?php

namespace Yard\BAG\GravityForms;

use GF_Fields;
use WP_REST_Request;
use Yard\BAG\Foundation\ServiceProvider;
use Yard\BAG\GravityForms\BAGAddress\BAGAddressField;
use Yard\BAG\GravityForms\BAGAddress\BAGFieldSettings;
use Yard\BAG\GravityForms\BAGAddress\BAGLookup;

class GravityFormsServiceProvider extends ServiceProvider
{
    /**
     * Register all necessities for GravityForms.
     */
    public function register(): void
    {
        add_action('rest_api_init', [$this, 'registerRestRoute']);

        add_action('gform_loaded', function () {
            GF_Fields::register(new BAGAddressField());
        }, 5);

        add_action('init', [new BAGFieldSettings(), 'register']);
    }

    /**
     * The lookup lives on the REST API instead of admin-ajax.php, which sits under /wp-admin
     * and is blocked for visitors on sites that IP-restrict wp-admin.
     */
    public function registerRestRoute(): void
    {
        register_rest_route(BAGLookup::REST_NAMESPACE, BAGLookup::REST_ROUTE, [
            'methods'             => 'POST',
            'callback'            => fn (WP_REST_Request $request) => BAGLookup::make($request)->execute(),
            'permission_callback' => '__return_true',
            'args'                => [
                'zip'                => ['type' => 'string'],
                'homeNumber'         => ['type' => 'string'],
                'homeNumberAddition' => ['type' => 'string'],
                'identifier'         => ['type' => 'string'],
            ],
        ]);
    }
}
