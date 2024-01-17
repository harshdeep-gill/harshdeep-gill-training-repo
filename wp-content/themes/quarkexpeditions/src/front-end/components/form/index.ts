/**
 * Global variables.
 */
const { customElements, HTMLElement, intlTelInput, zustand } = window;

// types added tentatively
const { getState, setState, subscribe } = zustand.stores.global;

/**
 * External dependency.
 */
import '@travelopia/web-components/dist/form';
import { TPFormElement } from '@travelopia/web-components';

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
	private form: HTMLFormElement | null;
	private intelTelInputInstance: typeof intlTelInput | null;
	private phoneInputEl: HTMLInputElement | null;
	private phoneValidationEl: HTMLElement | null;
	private formFieldPhoneContainerEl: HTMLElement | null;
	private formFieldPhoneParentContainerEl: HTMLElement | null;
	private submitButton: HTMLButtonElement | null;
	private originalButtonText: string = '';
	private recaptchaTokenField: HTMLInputElement | null;
	private thankYouPageUrl: string | null;
	private deviceWidthField: HTMLInputElement | null;
	private deviceField: HTMLInputElement | null;
	private geolocationField: HTMLInputElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.tpForm = this.querySelector( 'tp-form' );
		this.form = this.querySelector( 'form' );
		this.phoneInputEl = this.querySelector( '.form__phone' );
		this.phoneValidationEl = this.querySelector( '.form__phone_validation_error' );
		this.formFieldPhoneContainerEl = this.querySelector( '.form-field__phone' );
		this.formFieldPhoneParentContainerEl = this.formFieldPhoneContainerEl?.parentNode as HTMLElement | null;
		this.submitButton = this.querySelector( '[type="submit"]' );
		this.recaptchaTokenField = this.querySelector( 'input[name="recaptcha_token"]' );
		this.thankYouPageUrl = this.getAttribute( 'thank-you-url' ) || '';
		this.deviceWidthField = this.querySelector( '.form__device-width' );
		this.deviceField = this.querySelector( '.form__device' );
		this.geolocationField = this.querySelector( 'input[name="fields[geolocation]"]' );

		// Initialize country codes.
		this.initializePhoneCountryCodes();

		// Set geolocation.
		this.setGeolocation();

		// Subscribe to visitor details changes.
		subscribe( this.prefill.bind( this ) );

		// Device width.
		this.updateDeviceWidth();
	}

	/**
	 * Connected Callback.
	 */
	connectedCallback() {
		// Events.
		this.tpForm?.addEventListener( 'submit', this.formSubmitted.bind( this ) );
		this.tpForm?.addEventListener( 'submit', ( event ) => this.handlePhoneValidation( event ) );
		window.addEventListener( 'resize', this.updateDeviceWidth.bind( this ) );

		// reCAPTCHA.
		if ( this.recaptchaTokenField && null !== this.ownerDocument.defaultView ) {
			this.ownerDocument.defaultView.addEventListener( 'DOMContentLoaded', () => setupRecaptcha() );
		}
	}

	/**
	 * Set Geolocation.
	 */
	setGeolocation() {
		// Get visitors details.
		const { visitorDetails } = getState();

		// Check if geolocation is already set, then early return.
		if ( ! visitorDetails.geolocation || ! this.geolocationField ) {
			// Early return.
			return;
		}

		// Set the value of geolocationField.
		this.geolocationField.value = JSON.stringify( visitorDetails.geolocation );
	}

	/**
	 * Initialize Phone Country Codes.
	 */
	initializePhoneCountryCodes() {
		// Check if we found intlTelInput.
		if ( ! this.phoneInputEl || ! intlTelInput ) {
			// We didn't, bail early.
			return;
		}

		// Get country code from visitor's details.
		const { visitorDetails } = getState();
		const { countryCode } = visitorDetails?.geolocation ?? {};

		// Initialize.
		this.intelTelInputInstance = new intlTelInput( this.phoneInputEl, {
			autoFormat: true,
			initialCountry: countryCode,
			preventInvalidNumbers: true,
			nationalMode: false,
		} );

		/**
		 * Set default phone number.
		 *
		 * If the visitor's phone number is already available ( i.e. it was set when he
		 * submitted the form for the first time ), set that as a default number.
		 */
		if ( visitorDetails.phone && this.intelTelInputInstance ) {
			this.intelTelInputInstance.setNumber( visitorDetails.phone );
		}

		// Add events for phone no update and validation.
		this.phoneInputEl.addEventListener( 'change', ( event ) => this.handlePhoneNoUpdate( event ) );
		this.phoneInputEl.addEventListener( 'keyup', ( event ) => this.handlePhoneNoUpdate( event ) );
		this.phoneInputEl.addEventListener( 'blur', ( event ) => this.handlePhoneValidation( event ) );
	}

	/**
	 * Event: 'change' | 'keyup'
	 *
	 * Handle Phone Number Update.
	 *
	 * @param {Event} event Event.
	 */
	handlePhoneNoUpdate( event: Event ) {
		// Is phone library initialized.
		if ( ! this.intelTelInputInstance ) {
			// Nope, bail early.
			return;
		}

		// Remove non-numeric character.
		const targetEl = event.target as HTMLInputElement;
		targetEl.value = this.intelTelInputInstance.getNumber();

		// Reset error message.
		this.resetPhoneErrorMessage();
	}

	/**
	 * Handle Phone Validation.
	 *
	 * @param {Event} event Event.
	 */
	handlePhoneValidation( event: Event ) {
		// Check if we have all elements.
		if (
			! this.phoneInputEl ||
			! this.intelTelInputInstance ||
			! this.phoneValidationEl ||
			! this.formFieldPhoneContainerEl
		) {
			// We don't, bail early.
			return;
		}
		const errorMap = [ 'Invalid number', 'Invalid country code', 'Too short', 'Too long', 'Invalid number' ];

		// First reset previous phone error message.
		this.resetPhoneErrorMessage();

		// If input does not have a value, or it's a valid no. remove the class for validity and error.
		if ( ! this.phoneInputEl.value.trim() || this.intelTelInputInstance.isValidNumber() ) {
			// Did we get a valid number>
			if ( this.intelTelInputInstance.isValidNumber() ) {
				this.formFieldPhoneContainerEl.classList.remove( 'error' );
				this.formFieldPhoneContainerEl.classList.add( 'valid' );

				// Need to update the parent class too if the phone number field is wrapped inside 'form-field' class.
				if ( this.formFieldPhoneParentContainerEl?.classList.contains( 'form-field' ) ) {
					this.formFieldPhoneParentContainerEl.classList.remove( 'error' );
					this.formFieldPhoneParentContainerEl.classList.add( 'valid' );
				}
			} else if ( 'required' === this.phoneInputEl.getAttribute( 'required' ) && 'submit' === event.type ) {
				this.formFieldPhoneContainerEl.classList.remove( 'valid' );
				this.formFieldPhoneContainerEl.classList.add( 'error' );

				// Need to update the parent class too if the phone number field is wrapped inside 'form-field' class.
				if ( this.formFieldPhoneParentContainerEl?.classList.contains( 'form-field' ) ) {
					this.formFieldPhoneParentContainerEl.classList.remove( 'error' );
					this.formFieldPhoneParentContainerEl.classList.add( 'valid' );
				}

				// Mark as required.
				this.phoneValidationEl.innerHTML = 'This field is required';
			}

			// Bail at this point.
			return;
		}

		// Get validation error and add that to the error element.
		const errorCode = this.intelTelInputInstance.getValidationError();
		this.formFieldPhoneContainerEl.classList.add( 'error' );
		this.phoneValidationEl.innerHTML = errorMap[ errorCode ] || 'Invalid number';
	}

	/**
	 * Reset Phone Error Message.
	 */
	resetPhoneErrorMessage() {
		// Do we have all required elements?
		if (
			! this.phoneInputEl ||
			! this.phoneValidationEl ||
			! this.formFieldPhoneContainerEl
		) {
			// Nope, bail early.
			return;
		}

		// Remove error, valid class and error message.
		this.formFieldPhoneContainerEl.classList.remove( 'error' );
		this.formFieldPhoneContainerEl.classList.remove( 'valid' );

		// Need to update the parent class too if the phone number field is wrapped inside 'form-field' class.
		if ( this.formFieldPhoneParentContainerEl?.classList.contains( 'form-field' ) ) {
			this.formFieldPhoneParentContainerEl.classList.remove( 'error' );
			this.formFieldPhoneParentContainerEl.classList.remove( 'valid' );
		}

		// Reset error message.
		this.phoneValidationEl.innerHTML = '';
	}

	/**
	 * Event: Form submitted.
	 *
	 * @param {Event} e Form submit event.
	 */
	formSubmitted( e: SubmitEvent ) {
		// Prevent default action.
		e.preventDefault();

		// Validate phone.
		if ( this.intelTelInputInstance && ! this.intelTelInputInstance.isValidNumber() ) {
			// Bail if the phone number isn't valid.
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
			if ( this.submitButton ) {
				this.submitButton.innerHTML = this.originalButtonText;
				this.submitButton.removeAttribute( 'disabled' );
			}
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
			this.saveVisitorInfo( formData );

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
				response.json().then( ( result ) => {
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

	/**
	 * Save visitor's info.
	 *
	 * @param {FormData} formData Form data.
	 */
	saveVisitorInfo( formData: FormData ) {
		// Get visitor details.
		const { visitorDetails } = getState();
		const details: VisitorInfo = {
			email: formData.get( 'email_address' ) as string, // Required.
			full_name: formData.get( `fields[full_name]` ) as string,
			firstname: formData.get( `fields[firstname]` ) as string,
			lastname: formData.get( `fields[lastname]` ) as string,
			phone: formData.get( `fields[phone]` ) as string,
		};

		// Update visitor.
		setState( {
			visitorDetails: {
				...visitorDetails,
				...details,
			},
		} );
	}

	/**
	 * Store visitor's device width.
	 */
	updateDeviceWidth() {
		// Update device width.
		if ( this.deviceWidthField ) {
			this.deviceWidthField.value = `${ String( window.outerWidth ) }x${ String( window.outerHeight ) }`;
		}

		// Try to determine device.
		if ( this.deviceField ) {
			let device = 'Phone';

			// Determine based on width.
			if ( window.innerWidth > 1100 ) {
				device = 'Desktop';
			} else if ( window.innerWidth > 700 && window.innerWidth < 1100 ) {
				device = 'Tablet';
			}

			// Update value.
			this.deviceField.value = device;
		}
	}

	/**
	 * Prefill form.
	 */
	prefill() {
		// Get state.
		const { visitorDetails, campaignParams, httpReferrer } = getState();

		// Adwords data.
		if ( campaignParams && 0 !== Object.keys( campaignParams ).length ) {
			for ( const key in campaignParams ) {
				const field = this.querySelector( `.form__${ key.replace( '_', '-' ) }` );

				// Update adwords data.
				if ( field && 'value' in field ) {
					field.value = decodeURIComponent( campaignParams[ key ] );
				}
			}
		}

		// httpReferrer data.
		if ( httpReferrer && 0 !== Object.keys( httpReferrer ).length ) {
			const httpReferrerField = this.querySelector( '.form__http-referrer' );

			// Update referrer field.
			if ( httpReferrerField && 'value' in httpReferrerField ) {
				httpReferrerField.value = httpReferrer;
			}
		}

		// Visitor details.
		if ( ! visitorDetails ) {
			// We didn't find any, bail early.
			return;
		}

		// Prefill email.
		if ( visitorDetails?.email ) {
			const field = this.querySelector( `[name="email_address"]` );

			// Update email address.
			if ( field && 'value' in field ) {
				field.value = visitorDetails.email;
			}
		}

		// Prefill other fields.
		for ( const [ key, value ] of Object.entries( visitorDetails ) ) {
			// Phone no. is handled differently after phone library is initialized.
			if ( key && value && 'phone' !== key ) {
				const field = this.querySelector( `[name="fields[${ key }]"]` );

				// Update value.
				if ( field && 'value' in field ) {
					field.value = value;
				}
			}
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-form', Form );
