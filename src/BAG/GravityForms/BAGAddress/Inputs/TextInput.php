<?php

namespace Yard\BAG\GravityForms\BAGAddress\Inputs;

use GFCommon;

class TextInput extends AbstractInput
{

    /** @var int ID of the field in the form. */
    protected $fieldID = null;

    /** @var string Position of the field in the form. */
    protected $fieldPosition = 'full';

    /** @var string Field identifier. */
    protected $fieldName;

    /** @var string Field text. */
    protected $fieldText = '';

    /** @var string Set input to readonly. */
    protected $readonly = '';

    /** @var string Add css class to input */
    protected $cssClass = '';

    /**
     * Set the value of fieldPosition
     *
     * @param string $fieldPosition
     *
     * @return  self
     */
    public function setFieldPosition(string $fieldPosition): self
    {
        $this->fieldPosition = $fieldPosition;

        return $this;
    }

    /**
     * Set the value of fieldID
     *
     * @param int $fieldID
     *
     * @return  self
     */
    public function setFieldID(int $fieldID): self
    {
        $this->fieldID = $fieldID;

        return $this;
    }


    /**
     * Set the value of fieldText
     *
     * @param string $fieldText
     *
     * @return  self
     */
    public function setFieldText(string $fieldText): self
    {
        $this->fieldText = $fieldText;

        return $this;
    }


    /**
     * Set the value of fieldName
     *
     * @param string $fieldName
     *
     * @return  self
     */
    public function setFieldName(string $fieldName): self
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * Set the value to readonly.
     *
     * @return self
     */
    public function setReadonly(): self
    {
        $this->readonly = 'readonly';

        return $this;
    }

    /**
     * Set the value of field.
     *
     * @param string $value
     *
     * @return  self
     */
    public function setFieldValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set the value of class
     *
     * @param string $cssClass
     *
     * @return  self
     */
    public function setClass(string $cssClass): self
    {
        $this->cssClass = $cssClass;

        return $this;
    }

    /**
     * Render the input.
     *
     * @return string
     */
    public function render(): string
    {
        if ($this->is_admin || ! rgar($this->getInput(), 'isHidden')) {
            if ($this->is_sub_label_above) {
                return "{$this->getSpanField()}
						{$this->getLabelField()}
                        {$this->getInputField()}
                    </span>";
            } else {
                return "{$this->getSpanField()}
                        {$this->getInputField()}
						{$this->getLabelField()}
                    </span>";
            }
        } else {
            return '';
        }
    }



    /**
     * Get the placeholder
     *
     * @return string
     */
    public function getPlaceholder(): string
    {
        return GFCommon::get_input_placeholder_attribute($this->getInput());
    }

    /**
     * Get the label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return '' != rgar($this->getInput(), 'customLabel') ? $this->getInput()['customLabel'] : $this->fieldText;
    }

    /**
     * Get the structuied span of the field.
     *
     * @return string
     */
    protected function getSpanField(): string
    {
        return "<span id='input_{$this->field->id}_{$this->form['id']}.{$this->fieldID}.container' class='ginput_{$this->fieldPosition} {$this->css_prefix} {$this->fieldID} {$this->cssClass}' {$this->style}>";
    }

    /**
     * Get the structured label of the field.
     *
     * @return string
     */
    protected function getLabelField(): string
    {
        return "<label for='{$this->field->id}_{$this->fieldID}' id='{$this->field->id}_{$this->fieldID}_label' {$this->sub_label_class_attribute}>{$this->getLabel()}</label>";
    }

    /**
     * Get the structured input.
     *
     * @return string
     */
    protected function getInputField(): string
    {
        return "<input
                    type='text'
                    data-name='{$this->fieldName}'
                    name='input_{$this->field->id}.{$this->fieldID}'
                    id='input_{$this->field->id}_{$this->form['id']}_{$this->fieldID}'
                    value='{$this->getValue()}'
                    {$this->field->get_tabindex()} {$this->disabled_text} {$this->readonly} {$this->getPlaceholder()} {$this->required_attribute} {$this->invalid_attribute}
                    aria-label='{$this->fieldName}'
                />";
    }
}
