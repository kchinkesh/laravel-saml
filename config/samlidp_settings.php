<?php

// For real IdP, you must set the url values in the 'idp' config to conform to the IdP's real urls.
$idp_host = env('SAML_IDP_HOST', 'http://localhost:8000/simplesaml');

return $settings = array(

    'strict' => true,

    // Enable debug mode (to print errors)
    'debug' => env('APP_DEBUG', false),

    // Service Provider Data that we are deploying
    'sp' => array(
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
        'x509cert' => env('SAML_SP_x509', ''),
        'privateKey' => env('SAML_SP_PRIVATEKEY', ''),
        'entityId' => env('SAML_SP_ENTITYID', ''),
        'assertionConsumerService' => array(
            'url' => '',
        ),
        'singleLogoutService' => array(
            'url' => '',
        ),
        'loginRoute' => '/login',
        'redirectAfterLogout' => '/'
    ),

    'idp' => array(
        'name'  => env('SAML_IDP_NAME', 'drdo_idp'),
        // Identifier of the IdP entity  (must be a URI)
        'entityId' => env('SAML_IDP_ENTITYID', $idp_host . '/saml/metadata.xml'),
        // SSO endpoint info of the IdP. (Authentication Request protocol)
        'singleSignOnService' => array(
            'url' => env('SAML_IDP_SSO_URL', $idp_host . '/saml/sso'),
        ),
        // SLO endpoint info of the IdP.
        'singleLogoutService' => array(
            'url' => env('SAML_IDP_SL_URL', $idp_host . '/saml/logout'),
        ),
        // Public x509 certificate of the IdP
        'x509cert' => env('SAML_IDP_x509', ''),
    ),

    /***
     *
     *  OneLogin advanced settings
     *
     *
     */
    // Security settings
    'security' => array(

        /** signatures and encryptions offered */

        // Indicates that the nameID of the <samlp:logoutRequest> sent by this SP
        // will be encrypted.
        'nameIdEncrypted' => false,

        // Indicates whether the <samlp:AuthnRequest> messages sent by this SP
        // will be signed.              [The Metadata of the SP will offer this info]
        'authnRequestsSigned' => false,

        // Indicates whether the <samlp:logoutRequest> messages sent by this SP
        // will be signed.
        'logoutRequestSigned' => false,

        // Indicates whether the <samlp:logoutResponse> messages sent by this SP
        // will be signed.
        'logoutResponseSigned' => false,

        /* Sign the Metadata
         False || True (use sp certs) || array (
            keyFileName => 'metadata.key',
            certFileName => 'metadata.crt'
        )
        */
        'signMetadata' => false,


        /** signatures and encryptions required **/

        // Indicates a requirement for the <samlp:Response>, <samlp:LogoutRequest> and
        // <samlp:LogoutResponse> elements received by this SP to be signed.
        'wantMessagesSigned' => false,

        // Indicates a requirement for the <saml:Assertion> elements received by
        // this SP to be signed.        [The Metadata of the SP will offer this info]
        'wantAssertionsSigned' => false,

        // Indicates a requirement for the NameID received by
        // this SP to be encrypted.
        'wantNameIdEncrypted' => false,

        // Authentication context.
        // Set to false and no AuthContext will be sent in the AuthNRequest,
        // Set true or don't present thi parameter and you will get an AuthContext 'exact' 'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport'
        // Set an array with the possible auth context values: array ('urn:oasis:names:tc:SAML:2.0:ac:classes:Password', 'urn:oasis:names:tc:SAML:2.0:ac:classes:X509'),
        'requestedAuthnContext' => true,
    ),

    // Contact information template, it is recommended to suply a technical and support contacts
    'contactPerson' => array(
        'technical' => array(
            'givenName' => 'Name',
            'emailAddress' => 'email@example.com'
        ),
        'support' => array(
            'givenName' => 'Name',
            'emailAddress' => 'email@example.com'
        ),
    ),

    // Organization information template, the info in en_US lang is recomended, add more if required
    'organization' => array(
        'en-US' => array(
            'name' => 'ORG',
            'displayname' => 'Organization',
            'url' => 'http://example.com'
        ),
    ),

    /* Interoperable SAML 2.0 Web Browser SSO Profile [saml2int]   http://saml2int.org/profile/current

   'authnRequestsSigned' => false,    // SP SHOULD NOT sign the <samlp:AuthnRequest>,
                                      // MUST NOT assume that the IdP validates the sign
   'wantAssertionsSigned' => true,
   'wantAssertionsEncrypted' => true, // MUST be enabled if SSL/HTTPs is disabled
   'wantNameIdEncrypted' => false,
*/

);
