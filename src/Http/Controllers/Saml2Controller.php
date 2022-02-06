<?php

namespace Kchinkesh\LaravelSaml\Http\Controllers;

use Kchinkesh\LaravelSaml\Events\SamlLoginEvent;
use Kchinkesh\LaravelSaml\Auth\SamlAuth;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Kchinkesh\LaravelSaml\Exceptions\SamlException;

class Saml2Controller extends Controller
{
    /**
     * Generate local sp metadata.
     *
     * @param SamlAuth $SamlAuth
     * @return \Illuminate\Http\Response
     */
    public function metadata(SamlAuth $SamlAuth)
    {
        $metadata = $SamlAuth->getMetadata();

        return response($metadata, 200, ['Content-Type' => 'text/xml']);
    }

    /**
     * Process an incoming saml2 assertion request.
     * Fires 'SamlLoginEvent' event if a valid user is found.
     *
     * @param SamlAuth $SamlAuth
     * @return \Illuminate\Http\Response
     */
    public function acs(SamlAuth $SamlAuth)
    {
        $errors = $SamlAuth->acs();

        if (!empty($errors)) {
            logger()->error('Saml error_detail', ['error' => $SamlAuth->getLastErrorReason()]);
            session()->flash('saml_error_detail', [$SamlAuth->getLastErrorReason()]);

            logger()->error('Saml error', $errors);
            session()->flash('saml_error', $errors);
            throw new SamlException(json_encode($errors));
        }
        $user = $SamlAuth->getSamlUser();

        event(new SamlLoginEvent($user, $SamlAuth));

        $redirectUrl = $user->getIntendedUrl();

        return redirect($redirectUrl);
    }

    /**
     * Process an incoming saml2 logout request.
     * Fires 'SamlLogoutEvent' event if its valid.
     * This means the user logged out of the SSO infrastructure, you 'should' log them out locally too.
     *
     * @param SamlAuth $SamlAuth
     * @return \Illuminate\Http\Response
     */
    public function sls(SamlAuth $SamlAuth)
    {
        $retrieveParametersFromServer = false;
        $errors = $SamlAuth->sls($retrieveParametersFromServer);
        if (!empty($errors)) {
            logger()->error('Saml2 error', $errors);
            session()->flash('saml2_error', $errors);
            throw new SamlException("Could not log out");
        }
        //redirect after logout
        return redirect(config('samlidp_settings.sp.redirectAfterLogout')); //may be set a configurable default
    }

    /**
     * Initiate a logout request across all the SSO infrastructure.
     *
     * @param SamlAuth $SamlAuth
     * @param Request $request
     */
    public function logout(SamlAuth $SamlAuth, Request $request)
    {
        $returnTo = $request->query('returnTo');
        $sessionIndex = $request->query('sessionIndex');
        $nameId = $request->query('nameId');
        $SamlAuth->logout($returnTo, $nameId, $sessionIndex); //will actually end up in the sls endpoint
        //does not return
    }

    /**
     * Initiate a login request.
     *
     * @param SamlAuth $SamlAuth
     */
    public function login(SamlAuth $SamlAuth)
    {
        $SamlAuth->login(config('samlidp_settings.sp.loginRoute'));
    }
}
