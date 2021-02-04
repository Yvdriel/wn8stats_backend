<?php


namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\StreamInterface;

class WoTAPIHandler extends Controller
{
    /** TODO Set global variables */

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
     * Holds the access token from World of Tanks if it's given.
     * Defaults as empty string
     *
     * @var string
     */
    protected $access_token;

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
        $this->access_token = '';
        $this->baseUrl = 'https://api.worldoftanks.eu/wot/';
        $this->application_id = '6ff2cfd3d752aed10e2e31b44c3095a8';
    }

    /**
     * Over here, we want to call the WoT API to get the AccountID based on the Username
     *
     * @param string $username
     * @return string
     */
    public function getAccountID(string $username)
    {
        $response = Http::get($this->baseUrl . 'account/list/', [
            'application_id' => $this->application_id,
            'search' => $username
        ]);

        $body = $response->json();

        return $body['data'][0]['account_id'];
    }

    /**
     * Get all the personal player data
     *
     * @param Request $request
     * @return mixed
     */
    public function getPlayerPersonalData(Request $request)
    {
        $this->account_id = $this->getAccountID($request['username']);

        if(isset($request['access_token']) && $request['access_token'] != '')
            $this->access_token = $request['access_token'];

        $response = Http::get($this->baseUrl . 'account/info/', [
            'application_id' => $this->application_id,
            'account_id' => $this->account_id,
            'access_token' => $this->access_token
        ]);

        $body = $response->json();

        return $body['data'];
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
