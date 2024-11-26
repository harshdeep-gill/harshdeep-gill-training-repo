/**
 * Tracking.
 */

// Campaign params.
export const campaignParams: string[] = [ 'utm_source', 'utm_term', 'utm_campaign', 'utm_keyword', 'utm_content', 'utm_theme', 'utm_medium', 'gclid', 'msclkid', 'fbid', 'fbclid', 'pclid' ];

/**
 * Get campaign params from URL.
 *
 * @return {Object} Campaign params.
 */
export const getCampaignParamsFromUrl = (): {[name: string]: string} => {
	// Initialize.
	const urlCampaignParams: {[name: string]: string} = {};

	// Get the campaign params from url and return.
	campaignParams.forEach( ( value: string ) => {
		// Split the URL and get params.
		const param = ( window.location.search.split( value + '=' )[ 1 ] || '' ).split( '&' )[ 0 ];

		// Check if param has a value.
		if ( '' !== param ) {
			urlCampaignParams[ value ] = param;
		}
	} );

	// Return.
	return urlCampaignParams;
};

/**
 * Get _ga cookie and return the value.
 *
 * @return {string} _ga cookie value.
 */
export const getGaCookie = (): string => {
	// Get the cookie.
	let gaCookie: string = document.cookie.split( '; ' ).find( ( cookie ) => cookie.match( /^_ga=GA\d\.\d\./ ) ) ?? '';

	// Check if we have a cookie.
	if ( gaCookie ) {
		// Split the cookie.
		const splitCookie = gaCookie.split( /^_ga=GA\d\.\d\.(.*)$/ );

		// Check if we got two values
		if ( splitCookie && splitCookie.length > 1 ) {
			gaCookie = splitCookie[ 1 ];
		}
	}

	// Return the cookie.
	return gaCookie;
};
