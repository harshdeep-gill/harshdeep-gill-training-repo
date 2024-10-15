/**
 * Global variables.
 */
const { zustand, dynamicPhoneNumber } = window;

// Get global state.
const { getState, subscribe } = zustand.stores.global;

/**
 * QuarkDrawerElement Class.
 */
class QuarkDynamicPhoneNumber extends HTMLElement {
	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Check if we have dynamic phone number data.
		if ( ! dynamicPhoneNumber || ! dynamicPhoneNumber?.default_phone_number ) {
			// No dynamic phone number data found, return.
			return;
		}

		// Subscribe to global state.
		subscribe( this.update.bind( this ) );

		// Update phone number.
		this.update( getState() );
	}

	/**
	 * Update phone number on page.
	 *
	 * @param {Object} state Global state.
	 */
	update( state: GlobalState ) {
		// Get phone number rule from global state.
		const { phoneNumberRule }: GlobalState = state;

		// Check if phone number rule exists.
		if ( ! phoneNumberRule ) {
			// Phone number rule does not exist.
			return;
		}

		// Get phone number and prefix.
		const phoneNumber = phoneNumberRule?.phone_number;

		// Update default phone number.
		this.updateDefaultPhoneNumber( phoneNumber );
	}

	/**
	 * Update default phone number on current page.
	 *
	 * @param {string} phoneNumber Phone number to update.
	 */
	updateDefaultPhoneNumber = ( phoneNumber: string = '' ) => {
		// Check if phone number is not found.
		if ( ! phoneNumber ) {
			// Return.
			return;
		}

		// Get anchor element.
		const anchor = this.querySelector( 'a' );

		// Check if anchor is not found.
		if ( ! anchor ) {
			// Return.
			return;
		}

		// Get existing phone number from anchor
		let existingPhoneNumber = anchor.getAttribute( 'href' );

		// Check if href attribute is not found.
		if ( ! existingPhoneNumber ) {
			// Return.
			return;
		}

		// Remove 'tel:' from existing phone number.
		existingPhoneNumber = existingPhoneNumber.replace( 'tel:', '' );

		// Check if phone number is already updated.
		if ( dynamicPhoneNumber.default_phone_number !== decodeURI( existingPhoneNumber ) ) {
			// Return.
			return;
		}

		// Update phone number.
		anchor.innerHTML = anchor.innerHTML.replace( dynamicPhoneNumber.default_phone_number, phoneNumber );
		anchor.href = `tel:${ phoneNumber }`;
	};
}

/**
 * Initialize.
 */
customElements.define( 'quark-dynamic-phone-number', QuarkDynamicPhoneNumber );
