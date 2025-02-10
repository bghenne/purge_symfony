import {Methods} from "../enums/methods";

/**
 * Fetch wrapper.
 */
export async function doRequest(
    url: string,
    method: string,
    parameters ?: FormData | object = {},
): Promise<object | void> {
    // const loader: HTMLElement|null = document.getElementById(elementName + '_loader');
    //
    // if (null !== loader) {
    //     loader.hidden = false;
    // }

    const requestParameters: RequestInit = {
        method: method,
        mode: 'cors'
    };

    if (Methods.GET === method) {
        // @ts-ignore The constructor of URLSearchParams *can* take a FormData object,
        // despite a slight difference in their interface. No suitable workarounds.
        url += '?' + new URLSearchParams(parameters).toString()
    } else {
        // need type casting
        requestParameters.body = parameters as FormData;
    }


    let response: Response;
    let responseBody;

    try {

        // A Fetch promise rejects if the request cannot reach the server (network failure, CORS misconfiguration…).
        // It does not reject on HTTP errors, including 5xx status codes (exception thrown, timeout…).
        response = await fetch(url, requestParameters);

        if (true === response.redirected) {
            // Too much time has passed since the last request: the PHP session has expired
            // and the user must be redirected to OpenID Connect for identification.
            location.reload();

            // The easiest way to handle that scenario is to let the function run until the redirect occurs.
            await new Promise(() => {
            });
        }

        // By convention, the middle uses the 200 HTTP status code even when a request fails,
        // and returns a JSON structure with the specific "error" property (see below).
        // These lines mainly handle 5xx status codes, which are issued when that property could not be set.
        if (false === response.ok) {
            return Promise.reject(response.statusText);
        }

        // Safeguard in case the middle returns a 204 status code instead of an empty JSON structure.
        if (204 === response.status) {
            return Promise.resolve();
        }

        // By convention, our JavaScript requests always return JSON data, never HTML.
        // That may change if the application is made "hypermedia-driven" with a tool like Turbo Drive.
        // Also note that "current browsers don't actually conform to the spec requirement
        // to set the body property to null for responses with no body."
        responseBody = await response.json();

        if (true === 'error' in responseBody) {
            return Promise.reject((responseBody as { error: string }).error);
        }

    } finally {
        // if (null !== loader) {
        //     loader.hidden = true;
        // }
    }

    return responseBody;
}

