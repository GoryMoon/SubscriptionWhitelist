<?php

namespace App\Patreon;

use App\Models\PatreonUser;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class PatreonAPI
{
    public PatreonUser $user;

    private string $api_endpoint = 'https://www.patreon.com/api/oauth2/v2/';
    private string $oauth_endpoint = 'https://www.patreon.com/api/oauth2/';

    /**
     * PatreonAPI constructor.
     *
     * @param PatreonUser $user
     */
    public function __construct(PatreonUser $user)
    {
        $this->user = $user;
    }

    /**
     * Gets the user identity.
     *
     * @see https://docs.patreon.com/#get-api-oauth2-v2-identity
     *
     * @param array $options The query options
     *
     * @return object|null The user if successful or null if not
     */
    public function getUser(array $options): ?object
    {
        return $this->getEndpoint('identity', $options);
    }

    /**
     * Gets the campaigns that the user has access to.
     *
     * @see https://docs.patreon.com/#get-api-oauth2-v2-campaigns
     *
     * @param array $options The query options
     *
     * @return object|null An object containing the campaigns of the user or null if failed
     */
    public function getCampaigns(array $options): ?object
    {
        return $this->getEndpoint('campaigns', $options);
    }

    /**
     * Gets specific data about a campaign.
     *
     * @see https://docs.patreon.com/#get-api-oauth2-v2-campaigns-campaign_id
     *
     * @param string $campaignID The id of a specific campaign
     * @param array $options The query options
     *
     * @return object|null An object containing the campaign details or null if failed
     */
    public function getCampaign(string $campaignID, array $options): ?object
    {
        return $this->getEndpoint("campaigns/$campaignID", $options);
    }

    /**
     * Gets members of a specific campaign.
     *
     * @see https://docs.patreon.com/#get-api-oauth2-v2-campaigns-campaign_id-members
     *
     * @param string $campaignID The id of a specific campaign
     * @param array $options The query options
     *
     * @return object|null An object containing the members of the campaign or null if failed
     */
    public function getMembers(string $campaignID, array $options): ?object
    {
        return $this->getEndpoint("campaigns/$campaignID/members", $options);
    }

    /**
     * Gets details about a specific member.
     *
     * @see https://docs.patreon.com/#get-api-oauth2-v2-members-id
     *
     * @param string $memberID The id of a specific member
     * @param array $options The query options
     *
     * @return object|null An object containing the member details or null if failed
     */
    public function getMember(string $memberID, array $options): ?object
    {
        return $this->getEndpoint("members/$memberID", $options);
    }

    /**
     * Gets the posts of a specific campaign.
     *
     * @see https://docs.patreon.com/#get-api-oauth2-v2-campaigns-campaign_id-posts
     *
     * @param string $campaignID The id of a specific campaign
     * @param array $options The query options
     *
     * @return object|null An object containing the posts of the campaign or null if failed
     */
    public function getPosts(string $campaignID, array $options): ?object
    {
        return $this->getEndpoint("campaigns/$campaignID/posts", $options);
    }

    /**
     * Gets details about a specific post.
     *
     * @see https://docs.patreon.com/#get-api-oauth2-v2-posts-id
     *
     * @param string $postID The id of a specific post
     * @param array $options The query options
     *
     * @return object|null An object containing the post details or null if failed
     */
    public function getPost(string $postID, array $options): ?object
    {
        return $this->getEndpoint("posts/$postID", $options);
    }

    /**
     * General helper method to get data from an endpoint
     * If the auth fails it retries once with a refreshed token before returning.
     *
     * @param string $endpoint The endpoint to get data from
     * @param array $options The query options
     *
     * @return object|null
     */
    private function getEndpoint(string $endpoint, array $options): ?object
    {
        for ($i = 0; $i < 2; ++$i) {
            $response = $this->get($endpoint, $this->buildQuery($options));
            if (is_object($response)) {
                return $response;
            } elseif (-1 === $response) {
                break;
            }
        }

        return null;
    }

    /**
     * Refreshes and updates the access and refresh token on the user.
     *
     * @return bool True if successful, False otherwise
     */
    private function refreshToken(): bool
    {
        $response = Http::baseUrl($this->oauth_endpoint)->asMultipart()->post('token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->user->refresh_token,
            'client_id' => config('services.patreon.client_id'),
            'client_secret' => config('services.patreon.client_secret'),
        ]);
        if ($response->successful()) {
            $json = $response->object();
            $this->user->access_token = $json->access_token;
            $this->user->refresh_token = $json->refresh_token;
            $this->user->save();

            return true;
        }

        return false;
    }

    /**
     * Setups the query array for Guzzle.
     *
     * @param array $options
     *
     * @return array
     */
    private function buildQuery(array $options): array
    {
        $q = [];

        foreach ($options as $key => $option) {
            if (is_array($option)) {
                $q[$key] = implode(',', $option);
            } else {
                $q[$key] = $option;
            }
        }

        return $q;
    }

    /**
     * Issue a GET request to the given URL.
     *
     * @param string $endpoint
     * @param array $options
     *
     * @return int|object
     */
    private function get(string $endpoint, array $options = [])
    {
        return $this->handleResponse($this->getRequest()->get($endpoint, $options));
    }

    /**
     * Prepares the request with the access token and base uri.
     *
     * @return PendingRequest
     */
    private function getRequest(): PendingRequest
    {
        return Http::withToken($this->user->access_token)->baseUrl($this->api_endpoint);
    }

    /**
     * Returns the json object if successful, -1 to retry and 0 to abort.
     *
     * @param Response $response
     *
     * @return int|object|null
     */
    private function handleResponse(Response $response)
    {
        if ($response->successful()) {
            return $response->object();
        }
        if (401 == $response->status()) {
            return $this->refreshToken() ? -1 : null;
        }

        return $response->object();
    }
}
