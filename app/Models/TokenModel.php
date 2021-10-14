<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;

abstract class TokenModel extends Model
{
    /**
     * Encrypts the token and sets it.
     *
     * @param string|null $value string
     */
    public function setRefreshTokenAttribute(?string $value)
    {
        if (is_null($value)) {
            $this->attributes['refresh_token'] = null;
        } else {
            $this->attributes['refresh_token'] = encrypt($value);
        }
    }

    /**
     * Decrypts the refresh token and returns it.
     *
     * @param string|null $value string
     *
     * @return string|null
     */
    public function getRefreshTokenAttribute(?string $value): ?string
    {
        if ( ! is_null($value)) {
            try {
                return decrypt($value);
            } catch (DecryptException $e) {
                report($e);

                return null;
            }
        }

        return null;
    }

    /**
     * Encrypts the token and sets it.
     *
     * @param string|null $value string
     */
    public function setAccessTokenAttribute(?string $value)
    {
        if (is_null($value)) {
            $this->attributes['access_token'] = null;
        } else {
            $this->attributes['access_token'] = encrypt($value);
        }
    }

    /**
     * Decrypts the access token and returns it.
     *
     * @param string|null $value string
     *
     * @return string|null
     */
    public function getAccessTokenAttribute(?string $value): ?string
    {
        if ( ! is_null($value)) {
            try {
                return decrypt($value);
            } catch (DecryptException $e) {
                report($e);

                return null;
            }
        }

        return null;
    }
}
