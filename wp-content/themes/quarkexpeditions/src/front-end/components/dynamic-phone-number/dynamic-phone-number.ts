/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

// Get global state.
const { getState, subscribe } = zustand.stores.global;

/**
 * QuarkDrawerElement Class.
 */
export class QuarkDynamicPhoneNumber extends HTMLElement {
	private readonly defaultPhoneNumber = '+1 (877) 585-1235';

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Subscribe to global state.
		subscribe( this.update.bind( this ) );

		// Update phone number on page.
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
		const prefix = phoneNumberRule?.prefix;
		const phoneNumberAndPrefix = prefix + ' : ' + phoneNumber;

		// Check if phone number found.
		if ( phoneNumber ) {
			// Phone number found, update all phone numbers on page.
			this.updateDynamicPhoneNumbers( phoneNumber );
		}

		// Check if prefix found.
		if ( prefix ) {
			// Prefix found, update all phone number prefix text on page.
			this.updateDynamicPhoneNumberPrefix( prefix );
		}

		// Check if prefix and phone number found.
		if ( prefix && phoneNumber ) {
			// Prefix and phone number found, update all phone number prefix text on page.
			this.updateDynamicPhoneNumberAndPrefix( phoneNumberAndPrefix );
		}

		// Update default phone number.
		this.updateDefaultPhoneNumber( phoneNumber );
	}

	/**
	 * Update all Phone number prefix text on current page.
	 *
	 * @param {string} prefix Phone number prefix to update.
	 */
	updateDynamicPhoneNumberPrefix = ( prefix: string ) => {
		// Get all nodes with class name '.dynamic-phone-prefix__text'.
		const phonePrefixNodes = this.getElementsByClassName( 'dynamic-phone-prefix__text' );

		// Loop through all nodes with class name 'dynamic-phone-prefix__text'.
		if ( phonePrefixNodes ) {
			for ( const phonePrefixNode of phonePrefixNodes ) {
				phonePrefixNode.innerHTML = prefix;
			}
		}
	};

	/**
	 * Update all Phone number and prefix text on current page.
	 *
	 * @param {string} phoneNumberAndPrefix Phone number prefix to update.
	 */
	updateDynamicPhoneNumberAndPrefix = ( phoneNumberAndPrefix: string ) => {
		// Get all nodes with class name '.dynamic-phone-number-and-prefix'.
		const phonePrefixNodes = this.getElementsByClassName( 'dynamic-phone-number-and-prefix' );

		// Loop through all nodes with class name 'dynamic-phone-number-and-prefix'.
		if ( phonePrefixNodes ) {
			for ( const phonePrefixNode of phonePrefixNodes ) {
				phonePrefixNode.innerHTML = phoneNumberAndPrefix;
			}
		}
	};

	/**
	 * Update all phone numbers on current page.
	 *
	 * @param {string} phoneNumber Phone number to update.
	 */
	updateDynamicPhoneNumbers = ( phoneNumber: string = '' ) => {
		// Get all nodes with class name '.dynamic-phone-number__text'.
		const phoneNumberNodes = this.getElementsByClassName( 'dynamic-phone-number__text' );

		// Loop through all nodes with class name 'dynamic-phone-number__text'.
		if ( phoneNumberNodes ) {
			for ( const phoneNumberNode of phoneNumberNodes ) {
				phoneNumberNode.innerHTML = phoneNumber;
			}
		}

		// Get all nodes with class name '.dynamic-phone-number__btn .btn__content'.
		let phoneNumberBtnNodes: NodeListOf<Element>|Element[] = this.querySelectorAll( '.dynamic-phone-number__btn .btn__content' );
		phoneNumberBtnNodes = [ ...phoneNumberBtnNodes ];

		// Loop through all nodes with class name 'dynamic-phone-number__btn'.
		if ( phoneNumberBtnNodes ) {
			for ( const phoneNumberBtnNode of phoneNumberBtnNodes ) {
				phoneNumberBtnNode.innerHTML = phoneNumber;
			}
		}

		// Get all nodes with class name 'dynamic-phone-number__link'.
		const phoneNumberLinkNodes = this.getElementsByClassName( 'dynamic-phone-number__link' );

		// Loop through all nodes with class name 'dynamic-phone-number__link'.
		if ( phoneNumberLinkNodes ) {
			for ( const phoneNumberLinkNode of phoneNumberLinkNodes ) {
				// Check if node is an anchor element.
				if ( phoneNumberLinkNode instanceof HTMLAnchorElement ) {
					phoneNumberLinkNode.href = `tel:${ phoneNumber }`;
				}
			}
		}
	};

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
		if ( this.defaultPhoneNumber !== existingPhoneNumber ) {
			// Return.
			return;
		}

		// Update phone number.
		anchor.innerHTML = anchor.innerHTML.replace( existingPhoneNumber, phoneNumber );
		anchor.href = `tel:${ phoneNumber }`;
	};
}
