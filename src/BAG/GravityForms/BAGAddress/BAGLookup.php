<?php

namespace Yard\BAG\GravityForms\BAGAddress;

use GF_Field;
use WP_Error;

class BAGLookup
{
    protected string $zip;
    protected string $homeNumber;
    protected string $homeNumberAddition;
    protected ?GF_Field $field;

    protected string $url = 'https://api.pdok.nl/bzk/locatieserver/search/v3_1/free';

    final public function __construct()
    {
        $this->zip                = $this->cleanUpInput('zip');
        $this->homeNumber         = $this->cleanUpInput('homeNumber');
        $this->homeNumberAddition = $this->cleanUpInput('homeNumberAddition');
        $this->url                = $this->parseURLvariables();
        $this->field              = $this->getField();
    }

    /**
     * Static constructor
     *
     * @return self
     */
    public static function make(): self
    {
        return new static();
    }

    /**
     * Actually execute the remote request.
     * @return string
     */
    public function execute(): string
    {
        return $this->processResponse($this->call());
    }

    /**
     * Process the incoming response object.
     * @param  array|WP_Error $response HTTP response.
     * @return string
     */
    protected function processResponse($response): string
    {
        if (is_wp_error($response)) {
            return wp_send_json_error([
                'message' => 'Er is een fout opgetreden',
                'results' => $response
            ]);
        }

        $body     = wp_remote_retrieve_body($response);
        $data     = json_decode($body);
        $response = $data->response;

        if (1 > $response->numFound) {
            return wp_send_json_error([
                'message' => __('No results found', 'owc-gravityforms-bag-address'),
                'results' => [],
            ]);
        }

        $results = [];

        foreach ($response->docs as $doc) {
            $address = new BAGEntity($doc);

            if (
                $this->lookupLimitedToMunicipality() &&
                $this->addressInMunicipality($address) === false
            ) {
                return wp_send_json_error([
                    'message' => __('The requested address is not within the limits of the municipality.', 'owc-gravityforms-bag-address'),
                    'results' => []
                ]);
            }

            $results[] = $address->toArray();
        }

        // Remove duplicate results
        $results = array_values(
            array_map(
                'unserialize',
                array_unique(
                    array_map('serialize', $results)
                )
            )
        );

        $count = count($results);

        return wp_send_json_success([
            'message' => sprintf(
                _n(
                    '%d result found',
                    '%d results found',
                    $count,
                    'owc-gravityforms-bag-address'
                ),
                $count
            ),
            'results' => $results
        ]);
    }

    protected function lookupLimitedToMunicipality(): bool
    {
        return property_exists($this->field, 'municipality_limit')
            && ! empty($this->field->municipality_limit);
    }

    protected function addressInMunicipality(BAGEntity $address): bool
    {
        return $this->field->municipality_limit === $address->gemeentecode;
    }

    /**
     * Makes the call to remote.
     * @return WP_Error|array The response or WP_Error on failure.
     */
    protected function call()
    {
        return wp_remote_get($this->url, ['timeout' => 10]);
    }

    /**
     * Removes any spaces, escapes weird characters.
     */
    protected function cleanUpInput(string $input = ''): string
    {
        $output = esc_attr(trim(($_POST[$input] ?? '')));
        $output = preg_replace('/\s/', '', $output);

        return (string) $output;
    }

    /**
     * Gets the GF_Field instance from the identifier in the request.
     */
    protected function getField(): ?GF_Field
    {
        preg_match('/field_([\d]+)_([\d]+)/', $this->cleanUpInput('identifier'), $matches);

        if (empty($matches) || empty($matches[0])) {
            return null;
        }

        [, $formId, $fieldId] = $matches;

        $field = \GFAPI::get_field($formId, $fieldId);

        return $field ?: null;
    }

    /**
     * Parse the variables in the BAG url.
     */
    private function parseURLvariables(): string
    {
        $arg_and = ['type:adres'];
        $arg_or  = [];

        if ($this->zip) {
            $arg_and[] = "postcode:{$this->zip}";
        }

        if ($this->homeNumber) {
            $arg_and[] = "huisnummer:{$this->homeNumber}";
        }

        if ($this->homeNumberAddition) {
            $arg_or[] = [
                "huisnummertoevoeging:{$this->homeNumberAddition}",
                "huisletter:{$this->homeNumberAddition}",
            ];
        }

        $arg_or = array_map(function ($group) {
            return '( ' . implode(' or ', $group) . ' )';
        }, $arg_or);

        return add_query_arg([
            'q' => urlencode(implode(' and ', array_merge($arg_and, $arg_or))),
        ], $this->url);
    }
}
