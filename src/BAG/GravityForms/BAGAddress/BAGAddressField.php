<?php

namespace Yard\BAG\GravityForms\BAGAddress;

use GF_Field;
use Yard\BAG\GravityForms\BAGAddress\Inputs\TextInput;
use Yard\BAG\GravityForms\BAGAddress\Inputs\StringInput;

if (!\class_exists('\GFForms')) {
    die();
}

class BAGAddressField extends GF_Field
{
    /**
     * @var string $type The field type.
     */
    public $type = 'bag_address';

    /**
     * Return the field title, for use in the form editor.
     *
     * @return string
     */
    public function get_form_editor_field_title()
    {
        return esc_attr__('BAG Address', 'owc-gravityforms-bag-address');
    }

    /**
     * Returns the field button properties for the form editor. The array contains two elements:
     * 'group' => 'standard_fields' // or  'advanced_fields', 'post_fields', 'pricing_fields'
     * 'text'  => 'Button text'
     *
     * Built-in fields don't need to implement this because the buttons are added in sequence in GFFormDetail
     *
     * @return array
     */
    public function get_form_editor_button()
    {
        return [
            'group' => 'advanced_fields',
            'text'  => $this->get_form_editor_field_title(),
        ];
    }

    /**
     * Returns the class names of the settings which should be available on the field in the form editor.
     *
     * @return array
     */
    public function get_form_editor_field_settings()
    {
        return [
            'sub_label_placement_setting',
            'input_placeholders_setting',
            'rules_setting',
            'conditional_logic_field_setting',
            'label_setting',
            'rules_setting',
            'description_setting',
            'css_class_setting',
            'municipality_limit'
        ];
    }

    /**
     * This field type can be used when configuring conditional logic rules.
     *
     * @return bool
     */
    public function is_conditional_logic_supported(): bool
    {
        return true;
    }

    /**
     * Override this method to perform custom validation logic.
     *
     * Return the result (bool) by setting $this->failed_validation.
     * Return the validation message (string) by setting $this->validation_message.
     *
     * @param string|array $value The field value from get_value_submission().
     * @param array        $form  The Form Object currently being processed.
     *
     */
    public function validate($value, $form)
    {
        if ($this->isRequired) {
            $zip        = rgar($value, $this->id . '.1');
            $homeNumber = rgar($value, $this->id . '.2');

            if (empty($zip) && empty($homeNumber)) {
                $this->failed_validation  = true;
                $this->validation_message = empty($this->errorMessage) ? esc_html__('This field is required. Please enter a complete address.', 'owc-gravityforms-bag-address') : $this->errorMessage;
            }
        }

        $city    = rgar($value, $this->id . '.4');
        $address = rgar($value, $this->id . '.5');
        if (empty($city) && empty($address)) {
            $this->failed_validation  = true;
            $this->validation_message = empty($this->errorMessage) ? esc_html__('This field is required. Please enter a complete address.', 'owc-gravityforms-bag-address') : $this->errorMessage;
        }
    }

    /**
     * Returns the field inner markup.
     *
     * @param array        $form  The Form Object currently being processed.
     * @param string|array $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
     * @param null|array   $entry Null or the Entry Object currently being edited.
     *
     * @return string
     */
    public function get_field_input($form, $value = '', $entry = null)
    {
        wp_register_script('bag_address-js', plugin_dir_url(GF_BAG_FILE) . 'resources/js/bag-address.js', ['jquery'], GF_BAG_VERSION, true);
        wp_enqueue_script('bag_address-js');
        wp_localize_script('bag_address-js', 'bag_address', ['ajaxurl' => admin_url('admin-ajax.php')]);

        $output = \implode(' ', \array_map(function ($item) {
            return $item->render();
        }, $this->getFields($value)));

        return \sprintf(
            '<div class="ginput_complex %1$s ginput_container ginput_container_bag_address" id="input_%2$d_%3$d">
                    %4$s
                <div class="gf_clear gf_clear_complex"></div>
            </div>',
            $this->class_suffix,
            $form['id'],
            \intval($this->id),
            $output
        );
    }

    /**
     * Returns the scripts to be included for this field type in the form editor.
     *
     * @return string
     */
    public function get_form_editor_inline_script_on_page_render()
    {

        // set the default field label for the field
        $script = \sprintf("function SetDefaultValues_%s(field) {
        field.label = '%s';
        field.inputs = [
			new Input(field.id + '.1', '%s'),
			new Input(field.id + '.2', '%s'),
			new Input(field.id + '.3', '%s'),
			new Input(field.id + '.4', '%s'),
			new Input(field.id + '.5', '%s')
		];
        }", $this->type, $this->get_form_editor_field_title(), 'Zip', 'HomeNumber', 'HomeNumberAddition', 'City', 'Address') . PHP_EOL;

        return $script;
    }

    /**
     * Format the entry value for display on the entry detail page and for the {all_fields} merge tag.
     *
     * Return a value that's safe to display for the context of the given $format.
     *
     * @param string|array $value    The field value.
     * @param string       $currency The entry currency code.
     * @param bool|false   $use_text When processing choice based fields should the choice text be returned instead of the value.
     * @param string       $format   The format requested for the location the merge is being used. Possible values: html, text or url.
     * @param string       $media    The location where the value will be displayed. Possible values: screen or email.
     *
     * @return string
     */
    public function get_value_entry_detail($value, $currency = '', $use_text = false, $format = 'html', $media = 'screen')
    {
        if (\is_array($value)) {
            $zip                = \trim(rgget($this->id . '.1', $value));
            $homeNumber         = \trim(rgget($this->id . '.2', $value));
            $homeNumberAddition = \trim(rgget($this->id . '.3', $value));
            $city               = \trim(rgget($this->id . '.4', $value));
            $address            = \trim(rgget($this->id . '.5', $value));

            $return = !empty($address) ? $address : "";
            $return .= !empty($homeNumber) ? " $homeNumber" : "";
            $return .= !empty($homeNumberAddition) ? " $homeNumberAddition" : "";
            $return .= !empty($zip) ? " $zip" : "";
            $return .= !empty($city) ? " $city" : "";
        } else {
            $return = '';
        }

        if ('html' === $format) {
            $return = esc_html($return);
        }

        return $return;
    }

    /**
     * Return all the fields available.
     *
     * @param array $value
     *
     * @return []
     */
    protected function getFields(array $value): array
    {
        return [
            (new TextInput($this, $value))
                ->setFieldID(1)
                ->setFieldName('zip')
                ->setFieldText(__('Postcode', 'owc-gravityforms-bag-address'))
                ->setFieldPosition('left'),
            (new TextInput($this, $value))
                ->setFieldID(2)
                ->setFieldName('homeNumber')
                ->setFieldText(__('Homenumber', 'owc-gravityforms-bag-address'))
                ->setFieldPosition('middle'),
            (new TextInput($this, $value))
                ->setFieldID(3)
                ->setFieldName('homeNumberAddition')
                ->setFieldText(__('Addition', 'owc-gravityforms-bag-address'))
                ->setFieldPosition('right'),
            (new StringInput())
                ->setContent(\sprintf('<span class="ginput_right"><input type="submit" class="js-bag-lookup | bag-search-button button" value="%s"></span>', __('Search', 'owc-gravityforms-bag-address'))),
            (new StringInput())
                ->setContent('<div class="result" style="display:block; height: 25px"></div>'),
            (new TextInput($this, $value))
                ->setFieldID(5)
                ->setFieldName('address')
                ->setFieldText(__('Address', 'owc-gravityforms-bag-address'))
                ->setReadonly()
                ->setFieldPosition('full'),
            (new TextInput($this, $value))
                ->setFieldID(4)
                ->setFieldName('city')
                ->setFieldText(__('City', 'owc-gravityforms-bag-address'))
                ->setReadonly()
                ->setFieldPosition('full')
        ];
    }
}
