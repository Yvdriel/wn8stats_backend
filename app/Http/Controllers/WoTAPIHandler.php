<?php


namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
     *
     * TODO Predefine the regions and predefine the calls that are allowed to make.
     */
    public function __construct()
    {
        /**
         * TODO Find some neat way to gather the region the players are in.
         */
        $this->access_token = '';
        $this->baseUrl = 'https://api.worldoftanks.eu/wot/';
        $this->application_id = '6ff2cfd3d752aed10e2e31b44c3095a8';
    }

    /**
     * Over here, we want to call the WoT API to get the AccountID based on the Username
     *
     * @param string $username
     * @return string
     * @throws Exception
     */
    public function getAccountID(string $username = ''): string
    {
        $response = Http::get($this->baseUrl . 'account/list/', [
            'application_id' => $this->application_id,
            'search' => $username
        ]);

        $body = $response->json();

        /** TODO Make this an error handler that is able to handle the WoT errors */
        if(isset($body['status']) && $body['status'] == 'error')
            Throw new Exception($body['error']);

        return $body['data'][0]['account_id'];
    }

    /**
     * Get all the personal player data
     *
     * @param Request $request
     * @return mixed
     * @throws Exception
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
     * Abstract API handler, this will handle most requests given to it.
     *
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function APIHandler(Request $request)
    {
        $this->account_id = $this->getAccountID($request['username']);

        if (isset($request['access_token']) && $request['access_token'] != '')
            $this->access_token = $request['access_token'];

        try {
            $response = Http::get($this->baseUrl . 'account/' . $request['requestType'], [
                'application_id' => $this->application_id,
                'account_id' => $this->account_id,
                'access_token' => $this->access_token
            ]);
        } catch(Exception $e) {
            Throw new Exception($e);
        }

        if($response->status() != 200)

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
