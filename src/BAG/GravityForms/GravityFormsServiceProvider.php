<?php

namespace Yard\BAG\GravityForms;

use GF_Fields;
use Yard\BAG\Foundation\ServiceProvider;
use Yard\BAG\GravityForms\BAGAddress\BAGLookup;
use Yard\BAG\GravityForms\BAGAddress\BAGAddressField;
use Yard\BAG\GravityForms\BAGAddress\BAGFieldSettings;

class GravityFormsServiceProvider extends ServiceProvider
{
    /**
     * Register all necessities for GravityForms.
     */
    public function register(): void
    {
        add_action('wp_ajax_nopriv_bag_address_lookup', [new BAGLookup(), 'execute']);
        add_action('wp_ajax_bag_address_lookup', [new BAGLookup(), 'execute']);

        add_action('gform_loaded', function () {
            GF_Fields::register(new BAGAddressField());
        }, 5);

        add_action('init', [new BAGFieldSettings(), 'register']);
    }
}
