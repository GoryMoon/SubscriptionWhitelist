<?php

namespace App\Patreon;

use App\Models\Channel;
use App\Models\PatreonUser;
use Illuminate\Support\Collection;
use stdClass;

class PatreonHelper
{
    /**
     * Gets a list of patreons currently pledging to a channel.
     *
     * @param Channel $channel
     *
     * @return string[]
     */
    public function getPatreons(Channel $channel): array
    {
        $patreon = $channel->owner->patreon;
        if (is_null($patreon) || is_null($patreon->campaign_id)) {
            return [];
        }

        $queries = $this->getQueries();
        $users = $this->getPatreonMembers($patreon);

        $users = self::filterActive($users, $queries->former);

        if ($queries->pledge) {
            $users = self::filterPaying($users);
        }

        if ( ! is_null($queries->tier)) {
            $users = self::filterTier($users, $queries->tier);
        } elseif (-1 < $queries->min || -1 < $queries->max) {
            $users = self::filterCentAmount($users, $queries->total, $queries->min, $queries->max);
        }

        return $users->map(function ($user) {
            return $user->name;
        })->toArray();
    }

    private static function filterActive(Collection $in, string $former): Collection
    {
        // Default filter out non-active
        $check = ['active_patron'];

        if ($former) {
            $check[] = 'former_patron';
        }

        return $in->filter(function ($user) use ($check) {
            return in_array($user->status, $check);
        });
    }

    private static function filterPaying(Collection $in): Collection
    {
        return $in->filter(function ($user) {
            return 'Paid' == $user->last_charge;
        });
    }

    private static function filterTier(Collection $in, string $tier): Collection
    {
        return $in->filter(function ($user) use ($tier) {
            return in_array($tier, $user->tiers);
        });
    }

    private static function filterCentAmount(Collection $in, int $total, int $min, int $max): Collection
    {
        return $in->filter(function ($user) use ($total, $min, $max) {
            $amount = $total ? $user->total : $user->cents;
            if ($min > -1 && $amount < $min) {
                return false;
            }
            if ($max > -1 && $amount > $max) {
                return false;
            }

            return true;
        });
    }

    /**
     * @param PatreonUser $patreon
     *
     * @return Collection<object>
     */
    private function getPatreonMembers(PatreonUser $patreon): Collection
    {
        $api = new PatreonAPI($patreon);

        $users = [];

        $options = [
            'fields[member]' => ['full_name', 'patron_status', 'last_charge_status', 'currently_entitled_amount_cents', 'campaign_lifetime_support_cents'],
            'include' => ['currently_entitled_tiers'],
        ];

        $cursor = null;
        while (true) {
            if ( ! is_null($cursor)) {
                $options['page[cursor]'] = $cursor;
            }
            $response = $api->getMembers($patreon->campaign_id, $options);

            if (isset($response->meta->pagination->cursors) && isset($response->meta->pagination->cursors->next)) {
                $cursor = $response->meta->pagination->cursors->next;
            } else {
                $cursor = null;
            }

            foreach ($response->data as $data) {
                $user = new stdClass();
                $user->name = $data->attributes->full_name;
                $user->status = $data->attributes->patron_status;
                $user->total = $data->attributes->campaign_lifetime_support_cents;
                $user->last_charge = $data->attributes->last_charge_status;
                $user->cents = $data->attributes->currently_entitled_amount_cents;

                $user->tiers = [];
                foreach ($data->relationships->currently_entitled_tiers->data as $tier) {
                    $user->tiers[] = $tier->id;
                }
                $users[] = $user;
            }

            if (is_null($cursor)) {
                break;
            }
        }

        return collect($users);
    }

    private function getQueries(): object
    {
        $req = request();
        $queries = new stdClass();
        $queries->pledge = 0 != $req->query('py', 0);
        $queries->former = 0 != $req->query('f', 0);
        $queries->total = 0 != $req->query('to', 0);

        $queries->min = $req->query('min', -1);
        $queries->max = $req->query('max', -1);

        $queries->tier = $req->query('t');

        return $queries;
    }
}
