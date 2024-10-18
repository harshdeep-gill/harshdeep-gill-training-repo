/**
 * Tracking.
 */

// Campaign params.
export const campaignParams: string[] = [ 'utm_source', 'utm_term', 'utm_campaign', 'utm_keyword', 'utm_content', 'utm_theme', 'utm_medium', 'gclid', 'msclkid', 'fbid', 'fbclid', "ga_client_id" ];

/**
 * Get campaign params from URL.
 *
 * @return {Object} Campaign params.
 */
export const getCampaignParamsFromUrl = (): any => {
	// Initialize.
	const urlCampaignParams: any = {};

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
 * Initialize tracking.
 */
export const initializeTracking = (): void => {
	// Get URL campaign params.
	const urlCampaignParams = getCampaignParamsFromUrl();

	// Trigger an event.
	window.dispatchEvent( new CustomEvent( 'visitor-tracked', { detail: urlCampaignParams } ) );
};

// Track when DOM is ready.
window.addEventListener( 'DOMContentLoaded', initializeTracking );
