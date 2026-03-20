<?php

namespace Yard\BAG\GravityForms\BAGAddress;

use function Yard\BAG\Foundation\Helpers\config;

use WP_Error;

class BAGLookup
{
    /**
     * Zip code string
     *
     * @var string
     */
    protected $zip = '';

    /**
     * Homenumber string
     *
     * @var string
     */
    protected $homeNumber = '';

    /**
     * Homenumber addition string
     *
     * @var string
     */
    protected $homeNumberAddition = '';

    /**
     * URL for BAG.
     *
     * @var string
     */
    private $url = 'https://api.pdok.nl/bzk/locatieserver/search/v3_1/free?q=';

    final public function __construct()
    {
        $this->zip                = $this->cleanUpInput('zip');
        $this->homeNumber         = $this->cleanUpInput('homeNumber');
        $this->homeNumberAddition = $this->cleanUpInput('homeNumberAddition');
        $this->url                = $this->parseURLvariables();
    }

    /**
     * Process the incoming response object.
     *
     * @param array|WP_Error $response HTTP response.
     *
     * @return string
     */
    protected function processResponse($response): string
    {
        if (is_wp_error($response)) {
            return wp_send_json_error(
                [
                    'message' => 'Er is een fout opgetreden',
                    'results' => $response
                ]
            );
        }
        $body     = wp_remote_retrieve_body($response);
        $data     = json_decode($body);
        $response = $data->response;
        if (1 > $response->numFound) {
            return wp_send_json_error(
                [
                    'message' => __('No results found', config('core.text_domain')),
                    'results' => [],
                ]
            );
        }

        if (1 === $response->numFound) {
            $address = new BAGEntity($response->docs[0]);
            return \wp_send_json_success([
                'message' => __('1 result found', config('core.text_domain')),
                'results' => [
                    'street'      => $address->straatnaam,
                    'houseNumber' => $address->huisnummer,
                    'city'        => $address->woonplaatsnaam,
                    'zip'         => $address->postcode,
                    'displayname' => $address->weergavenaam
                ]
            ]);
        }

        // If no addition was provided, try to find the result without any addition
        if ($this->homeNumberAddition === '') {
            $exactMatch = null;

            foreach ($response->docs as $doc) {
                if (!isset($doc->huisnummertoevoeging) && !isset($doc->huisletter)) {
                    $exactMatch = $doc;
                    break;
                }
            }

            if ($exactMatch !== null) {
                $address = new BAGEntity($exactMatch);
                return \wp_send_json_success([
                    'message' => __('1 result found', config('core.text_domain')),
                    'results' => [
                        'street'      => $address->straatnaam,
                        'houseNumber' => $address->huisnummer,
                        'city'        => $address->woonplaatsnaam,
                        'zip'         => $address->postcode,
                        'displayname' => $address->weergavenaam
                    ]
                ]);
            }
        }

        return wp_send_json_error(
            [
                'message' => __('Found too many results. Try to make the address more specific. For example with a house number addition', config('core.text_domain')),
                'results' => []
            ]
        );
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
     * Parse the variables in the BAG url.
     *
     * @return string
     */
    private function parseURLvariables(): string
	{
		$arg_and = ['type:adres'];
		$arg_or  = [];

		if (!empty($this->zip)) {
			$arg_and[] = "postcode:{$this->zip}";
		}

		if (!empty($this->homeNumber)) {
			$arg_and[] = "huisnummer:{$this->homeNumber}";
		}

		if (!empty($this->homeNumberAddition)) {
			$addition = strtoupper($this->homeNumberAddition);

			$arg_or[] = [
				"huisnummertoevoeging:{$addition}",
				"huisletter:{$addition}",
			];
		}

		$arg_or = array_map(function ($group) {
			return '( ' . implode(' or ', $group) . ' )';
		}, $arg_or);

		$query = implode(' and ', array_merge($arg_and, $arg_or));

		return $this->url . urlencode($query);
	}

    /**
     * Actually execute the remote request.
     *
     * @return string
     */
    public function execute(): string
    {
        return $this->processResponse($this->call());
    }

    /**
     * Makes the call to remote.
     *
     * @return WP_Error|array The response or WP_Error on failure.
     */
    protected function call()
    {
        return wp_remote_get($this->url, ['timeout' => 10]);
    }

    /**
     * Format the zipcode.
     * Removes any spaces, escapes weird characters.
     *
     * @param string $input
     *
     * @return string
     */
    protected function cleanUpInput($input = ''): string
    {
        $output = isset($_POST[$input]) ? esc_attr(trim($_POST[$input])) : '';
        $output = preg_replace('/\s/', '', $output);
        if (null === $output) {
            return '';
        }
        return $output;
    }
}
