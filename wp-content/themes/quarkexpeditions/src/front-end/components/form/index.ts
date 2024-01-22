/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * External dependencies.
 */
import '@travelopia/web-components/dist/form';
import { TPFormElement, TPFormSubmitElement } from '@travelopia/web-components';

/**
 * Internal Dependencies.
 */
import { getRecaptchaToken, setupRecaptcha } from './recaptcha';

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
		this.tpForm?.addEventListener( 'submit', this.formSubmitted.bind( this ) );

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
	formSubmitted( e: SubmitEvent ) {
		// Prevent default action.
		e.preventDefault();

		// Validate form.
		if ( ! this?.tpForm?.validate() ) {
			// Bail if the form isn't valid.
			return;
		}

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

			// Add reCAPTCHA token if field exists.
			if ( this.recaptchaTokenField ) {
				const recaptchaToken = await getRecaptchaToken();

				// Update reCAPTCHA value.
				if ( 'string' === typeof recaptchaToken ) {
					this.recaptchaTokenField.value = recaptchaToken;
				}
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
					if ( 'status' in result && 'success' === result.status ) {
						// Is there a thank-you page?
						if ( this.thankYouPageUrl ) {
							// Redirect to thank you page, if present.
							window.location.href = this.thankYouPageUrl;
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
			} ).catch( ( error ) => handleError( error ) );
		};

		// Send the request!
		sendRequest();
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-form', Form );
