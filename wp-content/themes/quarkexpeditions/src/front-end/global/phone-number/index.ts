/**
 * Phone Number.
 */

// Check if dynamic phone number data exists.
const { dynamicPhoneNumber } = window;

/**
 * Internal dependencies.
 */
export {};

// Wait for DOM to load.
window.addEventListener(
	'DOMContentLoaded',
	() => {
		// Check if we have dynamic phone number data.
		if ( ! dynamicPhoneNumber || ! dynamicPhoneNumber?.api_endpoint ) {
			// No dynamic phone number data found, return.
			return;
		}

		// Get phone number for this URL.
		fetch(
			`${ dynamicPhoneNumber.api_endpoint }`,
			{
				method: 'POST',
				cache: 'no-cache',
				headers: {
					'Content-Type': 'application/json',
				},
			}
		)
			.then( ( response ) => response.json() )
			.then( ( phoneNumberRule ) => {
				// Get phone number and prefix.
				const phoneNumber = phoneNumberRule?.phone_number;
				const prefix = phoneNumberRule?.prefix;
				const phoneNumberAndPrefix = prefix + ' : ' + phoneNumber;

				// Check if phone number found.
				if ( phoneNumber ) {
					// Phone number found, update all phone numbers on page.
					updateDynamicPhoneNumbers( phoneNumber );
				}

				// Check if prefix found.
				if ( prefix ) {
					// Prefix found, update all phone number prefix text on page.
					updateDynamicPhoneNumberPrefix( prefix );
				}

				// Check if prefix and phone number found.
				if ( prefix && phoneNumber ) {
					// Prefix and phone number found, update all phone number prefix text on page.
					updateDynamicPhoneNumberAndPrefix( phoneNumberAndPrefix );
				}
			} )
			.catch( () => {
				// Error fetching phone number, return.
			} );
	}
);

/**
 * Update all Phone number prefix text on current page.
 *
 * @param {string} prefix Phone number prefix to update.
 */
const updateDynamicPhoneNumberPrefix = ( prefix: string ) => {
	// Get all nodes with class name '.dynamic-phone-prefix__text'.
	const phonePrefixNodes = document.getElementsByClassName( 'dynamic-phone-prefix__text' );

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
const updateDynamicPhoneNumberAndPrefix = ( phoneNumberAndPrefix: string ) => {
	// Get all nodes with class name '.dynamic-phone-number-and-prefix'.
	const phonePrefixNodes = document.getElementsByClassName( 'dynamic-phone-number-and-prefix' );

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
const updateDynamicPhoneNumbers = ( phoneNumber: string = '' ) => {
	// Get all nodes with class name '.dynamic-phone-number__text'.
	const phoneNumberNodes = document.getElementsByClassName( 'dynamic-phone-number__text' );

	// Loop through all nodes with class name 'dynamic-phone-number__text'.
	if ( phoneNumberNodes ) {
		for ( const phoneNumberNode of phoneNumberNodes ) {
			phoneNumberNode.innerHTML = phoneNumber;
		}
	}

	// Get all nodes with class name '.dynamic-phone-number__btn .btn__content'.
	let phoneNumberBtnNodes: NodeListOf<Element>|Element[] = document.querySelectorAll( '.dynamic-phone-number__btn .btn__content' );
	phoneNumberBtnNodes = [ ...phoneNumberBtnNodes ];

	// Loop through all nodes with class name 'dynamic-phone-number__btn'.
	if ( phoneNumberBtnNodes ) {
		for ( const phoneNumberBtnNode of phoneNumberBtnNodes ) {
			phoneNumberBtnNode.innerHTML = phoneNumber;
		}
	}

	// Get all nodes with class name 'dynamic-phone-number__link'.
	const phoneNumberLinkNodes = document.getElementsByClassName( 'dynamic-phone-number__link' );

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
