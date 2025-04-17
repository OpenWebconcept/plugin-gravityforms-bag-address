<?php

namespace Yard\BAG\GravityForms\BAGAddress;

class BAGFieldSettings
{
    public function register()
    {
        add_action('gform_field_standard_settings', [$this, 'getFieldSettings']);
        add_action('gform_editor_js', [$this, 'getEditorJavascript']);
    }

    public function getFieldSettings(int $position): void
    {
        if (100 == $position) {
            ?>
            <li class="municipality_limit field_setting">
                <label for="field_municipality_limit" class="section_label">
                    <?php echo __('Limit address lookup to municipality borders', 'owc-gravityforms-bag-address'); ?>
                </label>
                <p>
                    <small>
                    <?php
                        $link = '<a href="https://www.cbs.nl/nl-nl/onze-diensten/methoden/classificaties/overig/gemeentelijke-indelingen-per-jaar" target="_BLANK">' . __('CBS municipality code', 'owc-gravityforms-bag-address') . '</a>';

            \printf(__('Enter the %s to which the results are limited.', 'owc-gravityforms-bag-address'), $link);
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
