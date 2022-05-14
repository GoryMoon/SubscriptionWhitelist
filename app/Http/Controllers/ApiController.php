<?php

namespace App\Http\Controllers;

use App\Repo\WhitelistRepository;
use Hashids;

class ApiController extends Controller
{
    /**
     * @var WhitelistRepository
     */
    private WhitelistRepository $repository;

    public function __construct(WhitelistRepository $repository)
    {
        $this->repository = $repository;
    }

    private function handleRequest(string $id, string $type, string $content, int $cache = 1800)
    {
        $ids = Hashids::decode($id);
        if (count($ids) > 0 && ! is_null($ids[0])) {
            $channel = $this->repository->getChannel($ids[0]);
            if ( ! is_null($channel)) {
                $list = $this->repository->getWhitelist($channel, $type, $id, $cache);

                return response($list, 200, ['Content-Type' => $content . '; charset=UTF-8']);
            }
        }

        return response()->json([
            'message' => 'Id not found',
        ], 404);
    }

    public function csv(string $_, string $id)
    {
        return $this->handleRequest($id, 'csv', 'text/csv');
    }

    public function nl(string $_, string $id)
    {
        return $this->handleRequest($id, 'nl', 'text/plain');
    }

    public function json_array(string $_, string $id)
    {
        return $this->handleRequest($id, 'json_array', 'application/json');
    }

    public function minecraft_uuid_csv(string $_, string $id)
    {
        return $this->handleRequest($id, 'minecraft_uuid_csv', 'text/csv');
    }

    public function minecraft_uuid_nl(string $_, string $id)
    {
        return $this->handleRequest($id, 'minecraft_uuid_nl', 'text/plain');
    }

    public function minecraft_uuid_json_array(string $_, string $id)
    {
        return $this->handleRequest($id, 'minecraft_uuid_json_array', 'application/json');
    }

    public function minecraft_csv(string $_, string $id)
    {
        return $this->handleRequest($id, 'minecraft_csv', 'text/csv');
    }

    public function minecraft_nl(string $_, string $id)
    {
        return $this->handleRequest($id, 'minecraft_nl', 'text/plain');
    }

    public function minecraft_twitch_nl(string $_, string $id)
    {
        return $this->handleRequest($id, 'minecraft_twitch_nl', 'text/plain');
    }

    public function minecraft_json_array(string $_, string $id)
    {
        return $this->handleRequest($id, 'minecraft_json_array', 'application/json');
    }

    public function minecraft_whitelist(string $_, string $id)
    {
        return $this->handleRequest($id, 'minecraft_whitelist', 'application/json');
    }

    public function steam_csv(string $_, string $id)
    {
        return $this->handleRequest($id, 'steam_csv', 'text/csv');
    }

    public function steam_nl(string $_, string $id)
    {
        return $this->handleRequest($id, 'steam_nl', 'text/plain');
    }

    public function steam_json_array(string $_, string $id)
    {
        return $this->handleRequest($id, 'steam_json_array', 'application/json');
    }

    public function patreon_csv(string $_, string $id)
    {
        return $this->handleRequest($id, 'patreon_csv', 'text/csv', 300);
    }

    public function patreon_nl(string $_, string $id)
    {
        return $this->handleRequest($id, 'patreon_nl', 'text/plain', 300);
    }

    public function patreon_json_array(string $_, string $id)
    {
        return $this->handleRequest($id, 'patreon_json_array', 'application/json', 300);
    }
}
