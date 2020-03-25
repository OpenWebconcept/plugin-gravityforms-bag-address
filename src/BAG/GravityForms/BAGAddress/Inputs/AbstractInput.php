<?php

namespace Yard\BAG\GravityForms\BAGAddress\Inputs;

use GFAPI;
use GFFormsModel;
use StdClass;

abstract class AbstractInput
{
    /** @var array form object */
    protected $form;

    /** @var stdClass */
    protected $field;

    /** @var string Set default value of input. */
    protected $value;

    /** @var bool */
    protected $is_admin;

    /** @var bool */
    protected $is_sub_label_above;

    /** @var string */
    protected $sub_label_class_attribute = '';

    /** @var string */
    protected $invalid_attribute = '';

    /** @var string */
    protected $disabled_text = '';

    /** @var string */
    protected $required_attribute = '';

    /** @var string */
    protected $style = '';

    /** @var string */
    protected $css_prefix = '';

    /**
     * Undocumented function
     *
     * @param \StdClass $field
     * @param array $value
     */
    public function __construct(StdClass $field, array $value)
    {
        $this->field                       = $field;
        $this->value                       = $value;
        $this->css_prefix                  = $this->field->is_entry_detail ? "_admin" : "";
        $this->is_admin                    = $this->field->is_entry_detail || $this->field->is_form_editor;
        $this->style                       = ($this->is_admin && rgar($this->getInput(), 'isHidden')) ? "style='display:none;'" : '';
        $this->disabled_text               = $this->field->is_form_editor ? "disabled='disabled'" : '';
        $this->required_attribute          = $this->field->isRequired ? 'aria-required="true"' : '';
        $this->invalid_attribute           = $this->field->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';
        $this->field_sub_label_placement   = $this->field->subLabelPlacement;
        $this->is_sub_label_above          = 'above' == $this->field_sub_label_placement || (empty($this->field_sub_label_placement) && 'above' == rgar(GFAPI::get_form($field->formId), 'subLabelPlacement'));
        $this->sub_label_class_attribute   = 'hidden_label' == $this->field_sub_label_placement ? "class='hidden_sub_label screen-reader-text'" : '';
    }

    /**
     * Get the submitted value
     *
     * @return string|array
     */
    public function getValue(): ?string
    {
        if (is_array($this->value)) {
            return esc_attr(rgget($this->field->id .'.'. $this->fieldID, $this->value));
        } else {
            return $this->value;
        }
    }

    /**
     * Return the input object.
     *
     * @return array|null
     */
    public function getInput()
    {
        return GFFormsModel::get_input($this->field, $this->field->id . '.'. $this->fieldID);
    }
}
