/**
 * Authenticated user.
 */
export interface Identity {
    userName: string,
    firstName: string,
    lastName: string,
    roles: Array<string>,
    environments: Array<string>,
    visibilityUnits : Array<string>,
}
