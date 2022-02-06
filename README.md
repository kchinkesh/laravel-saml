## Laravel - Saml

A Laravel package for Saml2 integration as a SP (service provider) based on  [OneLogin](https://github.com/onelogin/php-saml) toolkit, which is much lighter and easier to install. It doesn't need separate routes or session storage to work!

The aim of this library is to be as simple as possible. We won't mess with Laravel users, auth, session...  We prefer to limit ourselves to a concrete task. Ask the user to authenticate at the IDP and process the response. Same case for SLO requests.

## Installation - Composer

You can install the package via composer:

```
composer require kchinkesh/laravel-saml
```

Then publish the config files with 

```
php artisan vendor:publish --tag=saml-config
``` 

This will add the files `app/config/samlidp_settings.php`, which you will need to customize.

#### Configure laravel-saml to know about IDP

```env
SAML_IDP_ENTITYID=''
SAML_IDP_SSO_URL=''
SAML_IDP_SLO_URL=''
SAML_IDP_x509=''
```

### Usage

When you want your user to login, just redirect to the login route configured for the particular IDP, 
`route('saml_login')`.

Just remember that it does not use any session storage, so if you ask it to login it will redirect to the IDP whether the user is already logged in or not. For example, you can change your authentication middleware.

```php
public function handle($request, Closure $next)
{
    if ($this->auth->guest())
    {
        if ($request->ajax())
        {
            return response('Unauthorized.', 401);
        }
        else
        {
            return redirect('saml_login')
        }
    }
    return $next($request);
}
```

After login is called, the user will be redirected to the IDP login page. Then the IDP, which you have configured with an endpoint the library serves, will call back. That will process the response and fire an event when ready. The next step for you is to handle that event. You just need to login the user or refuse.

```php
Event::listen(function (\Kchinkesh\LaravelSaml\Events\SamlLoginEvent $event) {
    $user = $event->getSamlUser();
    $userData = [
        'id' => $user->getUserId(),
        'attributes' => $user->getAttributes(),
        'assertion' => $user->getRawSamlAssertion()
    ];
    $laravelUser = User::where('email',$user->getUserId())->first();
    //find user by ID or attribute
    //if it does not exist create it and go on  or show an error message
    Auth::login($laravelUser);
});
```
### Auth persistence

Be careful about necessary Laravel middleware for Auth persistence in Session.
Add the saml middleware to middleware groups
For exemple, it can be:

```php
# in App\Http\Kernel
protected $middlewareGroups = [
        'web' => [
            ...
        ],
        'api' => [
            ...
        ],
        'saml' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
        ],

```

### Log out
Now there are two ways the user can log out.
 + 1 - By logging out in your app: In this case you 'should' notify the IDP first so it closes global session.
 + 2 - By logging out of the global SSO Session. In this case the IDP will notify you on /idp/slo endpoint (already provided), if the IDP supports SLO

For case 1, initiate a logout by redirecting the user to the saml2_logout route (`route('saml_logout')`). Do not close the session immediately as you need to receive a response confirmation from the IDP (redirection). That response will be handled by the library at the `sls` route, and it will fire a `SamlLogoutEvent` event that you can use to complete the logout in the same way as with case 2 below.

For case 2 you will only receive the event. Both cases 1 and 2 receive the same `SamlLogoutEvent` event. 

Note that for case 2, you may have to manually save your session to make the logout stick (as the session is saved by middleware, but the OneLogin library will redirect back to your IDP before that happens)

```php
Event::listen(function (\Kchinkesh\LaravelSaml\Events\SamlLoginEvent $event) {
    Auth::logout();
    Session::save();
});
```

Note : This Packaged is an Updated Version on aacotroneo/laravel-saml2 which works with PHP 8.0