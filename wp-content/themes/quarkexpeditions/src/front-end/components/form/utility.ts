/**
 * Get the error message based on its code.
 *
 * @param {string} error Error code.
 *
 * @return {string} The error message.
 */
export const getErrorMessage = ( error: string = '' ): string => {
	// Get tpFormErrors.
	const { tpFormErrors } = window;

	// Check if tpFormErrors exists.
	if ( ! tpFormErrors ) {
		// Return early.
		return '';
	}

	// Check for error.
	if ( '' !== error && error in tpFormErrors && 'string' === typeof tpFormErrors[ error ] ) {
		// Return errors.
		return tpFormErrors[ error ];
	}

	// Return empty string.
	return '';
};
