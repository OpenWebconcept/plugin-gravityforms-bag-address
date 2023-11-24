<?php

namespace Yard\BAG\GravityForms\BAGAddress;

use function Yard\BAG\Foundation\Helpers\config;

class BAGFieldSettings
{
    public function register()
    {
        add_action('gform_field_standard_settings', [$this, 'getFieldSettings']);
        add_action('gform_editor_js', [$this, 'getEditorJavascript']);
    }

    public function getFieldSettings(int $position): void
    {
        if ($position == 100) {
            ?>
            <li class="municipality_limit field_setting">
                <label for="field_municipality_limit" class="section_label">
                    <?php echo __('Limit address lookup to municipality borders', config('core.text_domain')); ?>
                </label>
                <p>
                    <small>
                    <?php
                        $link = '<a href="https://www.cbs.nl/nl-nl/onze-diensten/methoden/classificaties/overig/gemeentelijke-indelingen-per-jaar" target="_BLANK">' . __('CBS municipality code', config('core.text_domain')) . '</a>';

                    \printf(__('Enter the %s to which the results are limited.', config('core.text_domain')), $link);
                    ?>
                    </small>
                </p>
                <input type="text" id="field_municipality_limit" name="field_municipality_limit" value="">
            </li>
            <?php
        }
    }

    public function getEditorJavascript(): void
    {
        ?>
        <script type='text/javascript'>
            jQuery(document).on("gform_load_field_settings", function(event, field, form) {
                jQuery('#field_municipality_limit').val(rgar(field, 'municipality_limit'));
            });

            jQuery(document).on('input', '#field_municipality_limit', function(e) {
                SetFieldProperty('municipality_limit', jQuery(e.currentTarget).val());
            });
        </script>
        <?php
    }
}
