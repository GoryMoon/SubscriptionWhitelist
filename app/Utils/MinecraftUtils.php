<?php


namespace App\Utils;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class MinecraftUtils
{

    private static $instance;
    private $http;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new MinecraftUtils;
        }
        return self::$instance;
    }

    /**
     * @return Client
     */
    public function getHttp() {
        if (is_null($this->http)) {
            $this->http = new Client(['base_uri' => "https://api.mojang.com/"]);
        }
        return $this->http;
    }

    /**
     * @param $method
     * @param $url
     * @param null $body
     * @return bool|mixed|null
     */
    private function executeRequest($method, $url, $body = null) {
        try {
            $request = new Request($method, $url, [], $body);
            $response = $this->getHttp()->send($request);
            $result = json_decode($response->getBody());
        } catch (RequestException $exception) {
            if (!is_null($exception->getResponse())) {
                if ($exception->getResponse()->getStatusCode() == 404) {
                    return false;
                }
                if ($exception->getResponse()->getStatusCode() == 429) {
                    return false;
                }
            }
            Log::error("MinecraftUtils: Unknown error occurred", [$exception]);
            return null;
        } catch (GuzzleException $e) {
            return false;
        }
        return $result;
    }

    /**
     * @param $name
     * @return bool|mixed|null
     */
    public function getProfile($name) {
        $response = $this->executeRequest('GET', "users/profiles/minecraft/$name");
        if (!is_null($response) && $response != false) {
            return $response;
        }
        return null;
    }

    /**
     * @param array[] $names
     * @return bool|mixed|null
     */
    public function getProfiles($names) {
        $response = $this->executeRequest('POST', "profiles/minecraft", json_encode($names));
        if (!is_null($response) && $response != false) {
            return $response;
        }
        return null;
    }

    public function getLatestName($uuid) {
        $response = $this->executeRequest('GET', "user/profiles/$uuid/names");
        if (!is_null($response) && $response != false) {
            return collect($response)->last()->name;
        }
        return null;
    }

}