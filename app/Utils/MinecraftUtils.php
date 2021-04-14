<?php

namespace App\Utils;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class MinecraftUtils
{
    private static MinecraftUtils $instance;
    private Client $http;

    protected function __construct()
    {
        $this->http = new Client(['base_uri' => 'https://api.mojang.com/']);
    }

    protected function __clone()
    {
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception('Cannot unserialize a singleton.');
    }

    /**
     * @return MinecraftUtils
     */
    public static function instance(): MinecraftUtils
    {
        if ( ! isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @param $method
     * @param $url
     * @param null $body
     *
     * @return bool|mixed|null
     */
    private function executeRequest($method, $url, $body = null)
    {
        try {
            $request = new Request($method, $url, [], $body);
            $response = $this->http->send($request);
            $result = json_decode($response->getBody());
        } catch (RequestException $exception) {
            if ( ! is_null($exception->getResponse())) {
                if (404 == $exception->getResponse()->getStatusCode()) {
                    return false;
                }
                if (429 == $exception->getResponse()->getStatusCode()) {
                    return false;
                }
            }
            Log::error('MinecraftUtils: Unknown error occurred', [$exception]);

            return null;
        } catch (GuzzleException $e) {
            return false;
        }

        return $result;
    }

    /**
     * @param $name
     *
     * @return bool|mixed|null
     */
    public function getProfile($name)
    {
        $response = $this->executeRequest('GET', "users/profiles/minecraft/$name");
        if ( ! is_null($response) && false != $response) {
            return $response;
        }

        return null;
    }

    /**
     * @param array[] $names
     *
     * @return bool|mixed|null
     */
    public function getProfiles(array $names)
    {
        $response = $this->executeRequest('POST', 'profiles/minecraft', json_encode($names));
        if ( ! is_null($response) && false != $response) {
            return $response;
        }

        return null;
    }

    /**
     * @param $uuid
     *
     * @return string|null
     */
    public function getLatestName($uuid): ?string
    {
        $response = $this->executeRequest('GET', "user/profiles/$uuid/names");
        if ( ! is_null($response) && false != $response) {
            return collect($response)->last()->name;
        }

        return null;
    }
}
