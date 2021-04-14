<?php


namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class WoTAPIHandler extends Controller
{
    /**
     * All available Realms from World of Tanks
     *
     * @var array
     */
    protected array $realms;

    /**
     * Application ID for WoT API
     *
     * @var string
     */
    protected string $application_id;

    /**
     * Player account ID
     *
     * @var int
     */
    protected int $account_id;

    /**
     * Holds the access token from World of Tanks if it's given.
     * Defaults as empty string
     *
     * @var string
     */
    protected string $access_token;

    /**
     * Base WoT API URL
     *
     * @var string
     */
    protected string $baseUrl;

    /**
     * WoTAPIHandler constructor
     *
     */
    public function __construct()
    {
        $this->realms = Config::get('app.Realms');
        $this->access_token = '';
        $this->baseUrl = '';
        $this->application_id = '6ff2cfd3d752aed10e2e31b44c3095a8';
    }

    /**
     * @param string $realm
     * @throws Exception
     */
    protected function setRealmUrl(string $realm)
    {
        if(!array_key_exists($realm, $this->realms)) {
            Throw new Exception($realm . ' is not a valid realm.');
        }

        $this->baseUrl = $this->realms[$realm];
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

        if(isset($body['status']) && $body['status'] == 'error') {
            Throw new Exception($body['error']);
        }

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
        try {
            $this->setRealmUrl($request['realm']);
            $this->account_id = $this->getAccountID($request['nickname']);

            if(isset($request['access_token']) && $request['access_token'] != '') {
                $this->access_token = $request['access_token'];
            }

            $response = Http::get($this->baseUrl . 'account/info/', [
                'application_id' => $this->application_id,
                'account_id' => $this->account_id,
                'access_token' => $this->access_token
            ]);

            $body = $response->json();
        } catch(Exception $e) {
            return $e->getMessage();
        }

        return $body['data'];
    }
}
