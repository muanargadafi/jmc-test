/**
 * Checks whether an error-like object resembles a fetch error
 * by containing either a 'response' or 'statusCode' property.
 *
 * @param {unknown} err - The error object to check.
 * @returns {boolean} True if the error has a response or statusCode property, false otherwise.
 */
export function isFetchErrorLike(err) {
  return typeof err === 'object' && err !== null && ('response' in err || 'statusCode' in err)
}

/**
 * Extracts the HTTP status code from a fetch-like error object.
 *
 * @param {unknown} err - The error object to inspect.
 * @returns {number | undefined} The status code if available, or undefined.
 */
export function getErrorStatus(err) {
  if (!isFetchErrorLike(err)) return undefined
  return err.response?.status ?? err.statusCode
}
