/**
 * Global variables.
 */
const { quark } = window;

// Check if it has already been set up.
let recaptchaSetup = false;

/**
 * Set up reCAPTCHA.
 */
export const setupRecaptcha = () => {
	// Check if we have reCAPTCHA settings.
	if ( ! quark?.recaptchaSiteKey || '' === quark.recaptchaSiteKey || recaptchaSetup ) {
		// Nope, bail early.
		return;
	}

	// Create reCAPCTHA tag and append it to BODY.
	const recaptchaScript = document.createElement( 'script' );
	recaptchaScript.type = 'text/javascript';
	recaptchaScript.src = `https://www.google.com/recaptcha/api.js?render=${ quark?.recaptchaSiteKey }`;
	recaptchaScript.defer = true;
	document.getElementsByTagName( 'body' )[ 0 ].appendChild( recaptchaScript );

	// Mark it as set up.
	recaptchaSetup = true;
};

/**
 * Get fresh reCAPTCHA token.
 */
export const getRecaptchaToken = () => {
	// Return a new Promise.
	return new Promise( ( resolve, reject ) => {
		// Check if we have reCAPTCHA available in window, and if it's not already set up.
		if ( 'grecaptcha' in window && recaptchaSetup ) {
			window.grecaptcha.ready( () => {
				// Set up reCAPTCHA when it's ready.
				window.grecaptcha
					.execute( quark?.recaptchaSiteKey, { action: 'leads' } )
					.then( ( token: string ) => resolve( token ) );
			} );
		} else {
			reject( 'reCAPTCHA not set up' );
		}
	} );
};
