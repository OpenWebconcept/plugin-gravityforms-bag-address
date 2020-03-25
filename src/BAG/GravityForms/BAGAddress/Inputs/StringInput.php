<?php

namespace Yard\BAG\GravityForms\BAGAddress\Inputs;

class StringInput
{
    /** @var string */
    protected $content;

    /**
     * Render the content.
     *
     * @return string
     */
    public function render(): string
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @param string $content
     *
     * @return  self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
