import { Methods } from "../enums/methods";

/**
 * Fetch wrapper.
 */
export async function doRequest<T>(
  url: string,
  method: string,
  parameters: FormData,
): Promise<T | void> {
  const requestParameters: RequestInit = {
    method: method,
    mode: "cors",
  };

  if (Methods.GET === method) {
    // @ts-expect-error The constructor of URLSearchParams *can* take a FormData object,
    // despite a slight difference in their interface. No suitable workarounds.
    url += "?" + new URLSearchParams(parameters).toString();
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
      // TODO: currently, the Fetch promise is rejected instead with the following reason:
      // Access to fetch at 'https://oauth-dev.gfp2000.com/authorize?client_id=open-web-purge&response_type=code&redirect_uri=http%3A%2F%2Flocalhost%3A823%2Fauto-sign-in&scope=openid+user_identity&state=d6810bae96ae279b4a44a4a455a0a3e0' (redirected from 'http://localhost:823/api/eligible-object') from origin 'http://localhost:823' has been blocked by CORS policy: No 'Access-Control-Allow-Origin' header is present on the requested resource. If an opaque response serves your needs, set the request's mode to 'no-cors' to fetch the resource with CORS disabled.
      location.reload();

      // The easiest way to handle that scenario is to let the function run until the redirect occurs.
      await new Promise(() => {});
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
    responseBody = await response.json();

    if (true === "error" in responseBody) {
      return Promise.reject((responseBody as { error: string }).error);
    }
  } catch (error) {
    return Promise.reject(error);
  }

  return responseBody;
}
