<?php

namespace Yard\BAG\GravityForms\BAGAddress;

use StdClass;

class BAGEntity
{
    /** @var StdClass */
    protected $data;

    /**
     * @param StdClass $data
     */
    public function __construct(StdClass $data)
    {
        $this->data = $data;
    }

    /**
     * Magic getter.
     *
     * @param string $key
     *
     * @return string
     */
    public function __get($key)
    {
        return $this->data->{$key} ?? '';
    }

    public function toArray(): array
    {
        return [
            'street'      => $this->data->straatnaam,
            'houseNumber' => $this->data->huisnummer,
            'city'        => $this->data->woonplaatsnaam,
            'zip'         => $this->data->postcode,
            'displayname' => $this->data->weergavenaam
        ];
    }
}
