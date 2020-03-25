<?php

namespace Yard\BAG\GravityForms\BAGAddress;

use GF_Field;
use Yard\BAG\GravityForms\BAGAddress\Inputs\StringInput;
use Yard\BAG\GravityForms\BAGAddress\Inputs\TextInput;

if (! class_exists('\GFForms')) {
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
        return esc_attr__('BAG Address', config('core.text_domain'));
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
     * @return void
     */
    public function validate($value, $form)
    {
        if ($this->isRequired) {
            $zip                  = rgar($value, $this->id . '.1');
            $homeNumber           = rgar($value, $this->id . '.2');

            if (empty($zip) && empty($homeNumber)) {
                $this->failed_validation  = true;
                $this->validation_message = empty($this->errorMessage) ? esc_html__('This field is required. Please enter a complete address.', config('core.text_domain')) : $this->errorMessage;
            }
        }

        $city                     = rgar($value, $this->id . '.4');
        $address                  = rgar($value, $this->id . '.5');
        $state                    = rgar($value, $this->id . '.6');
        if (empty($city) && empty($address) && empty($state)) {
            $this->failed_validation  = true;
            $this->validation_message = empty($this->errorMessage) ? esc_html__('This field is required. Please enter a complete address.', config('core.text_domain')) : $this->errorMessage;
        }
    }

    /**
     * Return all the fields available.
     *
     * @param string $value
     *
     * @return []
     */
    protected function getFields($value): array
    {
        return [
            (new TextInput($this, $value))
                ->setFieldID(1)
                ->setFieldName('zip')
                ->setFieldText(__('Postcode', config('core.text_domain')))
                ->setFieldPosition('left'),
            (new TextInput($this, $value))
                ->setFieldID(2)
                ->setFieldName('homeNumber')
                ->setFieldText(__('Homenumber', config('core.text_domain')))
                ->setFieldPosition('right'),
            (new TextInput($this, $value))
                ->setFieldID(3)
                ->setFieldName('homeNumberAddition')
                ->setFieldText(__('Homenumber addition', config('core.text_domain')))
                ->setFieldPosition('left'),
            (new StringInput())
            ->setContent(sprintf('<span class="ginput_right"><label>&nbsp;</label><input type="submit" class="bag-search-button button" id="bag-lookup" value="%s"></span>', __('Search', config('core.text_domain')))),
            (new StringInput())
            ->setContent('<div class="result" style="display:block; height: 25px"></div>'),
            (new TextInput($this, $value))
                ->setFieldID(5)
                ->setFieldName('address')
                ->setFieldText(__('Address', config('core.text_domain')))
                ->setReadonly()
                ->setFieldPosition('full'),
            (new TextInput($this, $value))
                ->setFieldID(4)
                ->setFieldName('city')
                ->setFieldText(__('City', config('core.text_domain')))
                ->setReadonly()
                ->setFieldPosition('left'),
            (new TextInput($this, $value))
                ->setFieldID(6)
                ->setFieldName('state')
                ->setFieldText(__('State', config('core.text_domain')))
                ->setReadonly()
                ->setFieldPosition('right')
        ];
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
        wp_register_script('bag_address-js', plugin_dir_url(GF_R_C_PLUGIN_FILE) . 'resources/js/bag-address.js');
        wp_enqueue_script('bag_address-js');
        wp_localize_script('bag_address-js', 'bag_address', ['ajaxurl' => admin_url('admin-ajax.php')]);

        $output = implode(' ', array_map(function ($item) {
            return $item->render();
        }, $this->getFields($value)));

        return "<div class='ginput_complex{$this->class_suffix} ginput_container ginput_container_bag_address' id='input_{$form['id']}_{intval($this->id)}'>
                    {$output}
                <div class='gf_clear gf_clear_complex'></div>
            </div>";
    }

    /**
     * Returns the scripts to be included for this field type in the form editor.
     *
     * @return string
     */
    public function get_form_editor_inline_script_on_page_render()
    {

        // set the default field label for the field
        $script = sprintf("function SetDefaultValues_%s(field) {
        field.label = '%s';
        field.inputs = [
			new Input(field.id + '.1', '%s'),
			new Input(field.id + '.2', '%s'),
			new Input(field.id + '.3', '%s'),
			new Input(field.id + '.4', '%s'),
			new Input(field.id + '.5', '%s'),
			new Input(field.id + '.6', '%s')
		];
        }", $this->type, $this->get_form_editor_field_title(), 'Zip', 'HomeNumber', 'HomeNumberAddition', 'City', 'Address', 'State') . PHP_EOL;

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
        if (is_array($value)) {
            $zip                         = trim(rgget($this->id . '.1', $value));
            $homeNumber                  = trim(rgget($this->id . '.2', $value));
            $homeNumberAddition          = trim(rgget($this->id . '.3', $value));
            $city                        = trim(rgget($this->id . '.4', $value));
            $address                     = trim(rgget($this->id . '.5', $value));
            $state                       = trim(rgget($this->id . '.6', $value));

            $return = $zip;
            $return .= ! empty($return) && ! empty($homeNumber) ? " $homeNumber" : $homeNumber;
            $return .= ! empty($return) && ! empty($homeNumberAddition) ? "$homeNumberAddition" : $homeNumberAddition;
            $return .= ! empty($return) && ! empty($city) ? " $city" : $city;
            $return .= ! empty($return) && ! empty($address) ? " $address" : $address;
            $return .= ! empty($return) && ! empty($state) ? " $state" : $state;
        } else {
            $return = '';
        }

        if ('html' === $format) {
            $return = esc_html($return);
        }

        return $return;
    }
}
