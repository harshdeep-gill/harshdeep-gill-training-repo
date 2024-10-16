/**
 * Global variables.
 */
const { zustand, location, dynamicPhoneNumber } = window;

/**
 * Internal dependencies
 */
const { setState, getState } = zustand.stores.global;

/**
 * Constants.
 */
export const CURRENCY_COOKIE_NAME = `STYXKEY_currency`;

/**
 * Initialize data.
 *
 * @param {Object} settings                    Settings.
 * @param {string} settings.currency           Currency.
 * @param {Object} settings.dynamicPhoneNumber Dynamic phone number.
 */
export const initialize = ( settings: {
    currency?: string,
	dynamicPhoneNumber?: object,
} ): void => {
	// Get current state.
	const currentState = getState();

	// Initialize: Add settings in state.
	setState( {
		...currentState,
		...settings,
		currency: getCurrencyFromCookie(),
		dynamicPhoneNumber: getUpdatedDynamicPhoneNumber(),
	} );
};

/**
 * Set currency to cookie.
 *
 * @param {string} currency Currency.
 */
export const setCurrency = ( currency: string ): void => {
	// Delete currency from cookie if currency is empty or USD.
	if ( ! currency || currency === 'USD' ) {
		document.cookie = `${ CURRENCY_COOKIE_NAME }=;path=/;max-age=0`;
	} else {
		// Set currency to cookie for a year.
		document.cookie = `${ CURRENCY_COOKIE_NAME }=${ currency };path=/;max-age=31536000`;
	}

	// Refresh page.
	location.reload();
};

/**
 * Get currency from cookie.
 */
export const getCurrencyFromCookie = (): string => {
	// Get currency from cookie.
	const cookie = document.cookie.split( '; ' ).find( ( row ) => row.startsWith( CURRENCY_COOKIE_NAME ) );

	// Return currency.
	return cookie ? cookie.split( '=' )[ 1 ] : 'USD';
};

/**
 * Get updated object of dynamic phone number.
 */
export const getUpdatedDynamicPhoneNumber = (): object => {
	// Return updated object of dynamic phone number.
	return {
		apiEndpoint: dynamicPhoneNumber.api_endpoint,
		defaultPhoneNumber: dynamicPhoneNumber.default_phone_number,
	};
};

/**
 * Get currency from state.
 */
export const getCurrency = (): string => {
	// Get currency from state.
	return getState().currency;
};

/**
 * Set phone number rule.
 *
 * @param {Object} phoneNumberRule Phone number rule.
 */
export const setPhoneNumberRule = ( phoneNumberRule: object ): void => {
	// Get current state.
	const currentState = getState();

	// Set phone number.
	setState( {
		...currentState,
		phoneNumberRule,
	} );
};
