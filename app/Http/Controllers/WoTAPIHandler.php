<?php


namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Psr\Http\Message\StreamInterface;

class WoTAPIHandler extends Controller
{
    /** TODO Set global variables */

    /**
     * Guzzle client
     *
     * @var object
     */
    protected $client;

    /**
     * Application ID for WoT API
     *
     * @var string
     */
    protected $application_id;

    /**
     * Player account ID
     *
     * @var int
     */
    protected $account_id;

    /**
     * Base WoT API URL
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * WoTAPIHandler constructor
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.worldoftanks.eu/wot/',
        ]);

        $this->baseUrl = 'https://api.worldoftanks.eu/wot/';
        $this->application_id = '6ff2cfd3d752aed10e2e31b44c3095a8';
    }

    /**
     * Over here, we want to call the WoT API to get the AccountID based on the Username
     *
     * @param Request $request
     * @throws GuzzleException
     */
    public function getAccountID(Request $request)
    {
        $response = $this->client->get('account/list/', [
            'query' => [
                'application_id' => $this->application_id,
                'search' => $request['username']
            ]
        ]);
        $body = json_decode($response->getBody(), true);

        return 'Account ID = ' . $body['data'][0]['account_id'];
        //Call WOT API with given username
    }

    /**
     *  TODO move this to another class
     * We want to check if the user exists within our Database
     *
     * @param array $accountDetails
     */
    public function checkIfUserExists(array $accountDetails)
    {
        //Check database if user exists
    }

}
