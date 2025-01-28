/**
 * Profile information about the authenticated end user.
 *
 * The data is retrieved through the web service _authentications/users_ [by the CommonLibrary](http://gitlab.gfp2000.com/open-web/common-library/blob/master/src/Authentication/Adapter/OpenIdConnect/AbstractAdapter.php#L176) and [stored in session](https://docs.laminas.dev/laminas-authentication/storage/) during the [OpenID Connect authentication](http://gitlab.gfp2000.com/open-web/common-library/blob/master/src/Authentication/Authentication.php#L198).
 *
 * When requested in JavaScript, tokens are excluded.
 */
export interface Identity {
    userName: string,
    firstName: string,
    lastName: string,
    roles: Array<string>,
    environments: Array<string>,
    visibilityUnits : Array<string>,
}
