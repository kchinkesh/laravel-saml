<?php

namespace Kchinkesh\LaravelSaml\Events;

use Kchinkesh\LaravelSaml\Auth\SamlAuth;
use Kchinkesh\LaravelSaml\Auth\SamlUser;

class SamlLoginEvent
{

    protected $user;
    protected $auth;

    function __construct(SamlUser $user, SamlAuth $auth)
    {
        $this->user = $user;
        $this->auth = $auth;
    }

    public function getSamlUser()
    {
        return $this->user;
    }

    public function getSamlAuth()
    {
        return $this->auth;
    }
}
