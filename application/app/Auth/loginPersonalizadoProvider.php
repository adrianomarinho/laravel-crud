<?php

namespace App\Auth;


use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class loginPersonalizadoProvider extends EloquentUserProvider
{

    // personalisa a entrada do password para senha
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 &&
            array_key_exists('senha', $credentials))) {
            return;
        }

        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (Str::contains($key, 'senha')) {
                continue;
            }

            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    // mudando a criptografia da senha
    // mantendo o padrao laravel trocando password por senha
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials['senha'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }
}
