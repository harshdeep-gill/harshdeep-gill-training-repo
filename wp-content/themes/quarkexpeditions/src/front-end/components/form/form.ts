/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * External dependencies.
 */
import '@travelopia/web-components/dist/form';
import { TPFormElement, TPFormSubmitElement } from '@travelopia/web-components';

/**
 * Internal Dependencies.
 */
import { getRecaptchaToken, setupRecaptcha } from './recaptcha';
import { getCampaignParamsFromUrl, getGaCookie } from './tracking';
import { getCampaignIdsFromCookies } from '../../global/store/actions';

/**
 * Form Class.
 */
export default class Form extends HTMLElement {
	/**
	 * Properties.
	 *
	 * @private
	 */
	private tpForm: TPFormElement | null;
	private tpFormSubmit: TPFormSubmitElement | null;
	private readonly form: HTMLFormElement | null;
	private readonly recaptchaTokenField: HTMLInputElement | null;
	private thankYouPageUrl: string = '';

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.tpForm = this.querySelector( 'tp-form' );
		this.tpFormSubmit = this.querySelector( 'tp-form-submit' );
		this.form = this.querySelector( 'form' );
		this.recaptchaTokenField = this.querySelector( 'input[name="recaptcha_token"]' );
		this.thankYouPageUrl = this.getAttribute( 'thank-you-url' ) || '';
	}

	/**
	 * Connected callback.
	 */
	connectedCallback(): void {
		// Events.
		this.tpForm?.addEventListener( 'submit-validation-success', this.formSubmitted.bind( this ) );

		// reCAPTCHA.
		if ( this.recaptchaTokenField && null !== this.ownerDocument.defaultView ) {
			this.ownerDocument.defaultView.addEventListener( 'DOMContentLoaded', () => setupRecaptcha() );
		}
	}

	/**
	 * Event: Form submitted.
	 *
	 * @param {Event} e Form submit event.
	 */
	formSubmitted( e: any ) {
		// Prevent default action.
		e.preventDefault();
		e.stopImmediatePropagation();

		// Trigger event before submit.
		this.dispatchEvent( new CustomEvent( 'submit', {
			detail: {
				form: this.form,
			},
		} ) );

		/**
		 * Handle an error during form submission.
		 *
		 * @param {string} error Error message.
		 */
		const handleError = ( error: string ) => {
			// Display error and enable button.
			alert( error ); // eslint-disable-line
			this.tpFormSubmit?.removeAttribute( 'submitting' );

			// Enable the whole form.
			this.form?.removeAttribute( 'inert' );
		};

		/**
		 * Handle a successful form submission.
		 *
		 * @param {any} result Result object.
		 */
		const handleSuccess = ( result: any ) => {
			// Reset the form.
			this.form?.reset();

			// Trigger event.
			this.dispatchEvent( new CustomEvent( 'submit-success', {
				detail: {
					result,
				},
			} ) );

			// Reset submit button.
			this.tpFormSubmit?.removeAttribute( 'submitting' );
		};

		/**
		 * Send request to the server.
		 */
		const sendRequest = async () => {
			// Check if we have a form.
			if ( ! this.form ) {
				// Nope, bail.
				return;
			}

			// Disable the form.
			this.form.setAttribute( 'inert', '' );

			// Add reCAPTCHA token if field exists.
			if ( this.recaptchaTokenField ) {
				// Get reCAPTCHA token.
				let recaptchaToken: string = '';

				// Wait for token.
				try {
					recaptchaToken = await getRecaptchaToken() as string;
				} catch ( error ) {
					console.log( error ); // eslint-disable-line
				}

				// Update reCAPTCHA value.
				this.recaptchaTokenField.value = recaptchaToken;
			}

			// Save visitor info.
			const formData = new FormData( this.form );

			// Send form request.
			fetch( this.form.getAttribute( 'data-action' ) ?? '', {
				method: this.form.getAttribute( 'method' ) ?? 'post',
				cache: 'no-cache',
				body: formData,
			} ).then( ( response ) => {
				// Did we get a valid response?
				if ( 200 !== response.status ) {
					handleError( `There was a problem submitting this form.\nPlease try again after some time.` );

					// Show error and bail.
					return;
				}

				// Parse response JSON.
				response.json().then( ( result ): void => {
					// Did we get a valid status?
					if ( 'success' in result && true === result.success ) {
						// Is there a thank-you page?
						if ( this.thankYouPageUrl ) {
							// Redirect to thank you page, if present.
							window.location.href = this.thankYouPageUrl;

							// Handle success.
							handleSuccess( result );
						} else {
							// If not, trigger an event.
							this.dispatchEvent( new CustomEvent( 'api-success', {
								detail: {
									result,
								},
							} ) );
						}
					}
				} );

				// Enable the form.
				this.form?.removeAttribute( 'inert' );
			} ).catch( ( error ) => handleError( error ) );
		};

		// Update tracking info.
		this.updateTrackingInfo();

		// Send the request!
		sendRequest();
	}

	/**
	 * Update tracking info.
	 */
	updateTrackingInfo(): void {
		// Get URL campaign params.
		let urlCampaignParams = getCampaignParamsFromUrl();

		// Check if we have any campaign params.
		if ( ! Object.keys( urlCampaignParams ).length ) {
			// Get campaign ids from cookies.
			urlCampaignParams = getCampaignIdsFromCookies();
		}

		// Get GA cookie.
		const gaCookie = getGaCookie();

		// Check if we have any campaign params.
		if ( urlCampaignParams ) {
			// Fill in the details into corresponding hidden fields.
			for ( const key in urlCampaignParams ) {
				const field: HTMLElement | null = this.querySelector( `.form__${ key.replace( '_', '-' ) }` );

				// Update Adwords data.
				if ( field && 'value' in field ) {
					field.value = decodeURIComponent( urlCampaignParams[ key ] );
				}
			}
		}

		// Get the detail cookie
		if ( gaCookie ) {
			const field: HTMLElement | null = this.querySelector( '.form__ga-client' );

			// Update the cookie field
			if ( field && 'value' in field ) {
				field.value = decodeURIComponent( gaCookie );
			}
		}
	}
}
