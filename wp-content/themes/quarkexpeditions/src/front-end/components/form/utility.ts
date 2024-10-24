/**
 * Get the error message based on its code.
 *
 * @param {string} error Error code.
 *
 * @return {string} The error message.
 */
export const getErrorMessage = ( error: string = '' ): string => {
	// Get tpFormErrors.
	const { tpFormErrors } = window;

	// Check if tpFormErrors exists.
	if ( ! tpFormErrors ) {
		// Return early.
		return '';
	}

	// Check for error.
	if ( '' !== error && error in tpFormErrors && 'string' === typeof tpFormErrors[ error ] ) {
		// Return errors.
		return tpFormErrors[ error ];
	}

	// Return empty string.
	return '';
};

/**
 * Prefill the form based on the mapping.
 *
 * @param {HTMLFormElement} form The form element.
 * @param {Record<string, Record<string, string>>} mapping The mapping of the form elements.
 */
export const prefillForm = ( form: HTMLFormElement,  mapping: Record<string, Record<string, string>> ): void => {
	// Get the URL search params.
	const searchParams = new URLSearchParams( window.location.search );

	// Get the form elements.
	const formElements = Object.keys( mapping );

	// Loop through the form elements.
	formElements.forEach(
		( elementItem ) => {
		// Get the value from the search params.
		const value = searchParams.get( elementItem );

		// Get the type, field name and event.
		const element = 'element' in mapping[ elementItem ] ? mapping[ elementItem ].element : 'input';
		const type = 'type' in mapping[ elementItem ] ? mapping[ elementItem ].type : '';
		const fieldName = 'fieldName' in mapping[ elementItem ] ? mapping[ elementItem ].fieldName : 'value';
		const event = 'event' in mapping[ elementItem ] ? mapping[ elementItem ].event : '';

		// Check if the value exists.
		if ( value ) {
			// Initialize the form element.
			let formElements: HTMLSelectElement | NodeListOf<HTMLInputElement> | HTMLInputElement | null;

			// Switch based on the element type.
			switch ( type ) {
				case 'checkbox':
					// Get the checkboxes.
					formElements = form.querySelectorAll( `${ element }[name="fields\\[${ fieldName }\\]\\[\\]"]` ) as NodeListOf<HTMLInputElement>;

					// If checkboxes are present.
					if ( formElements && formElements?.length ) {
						// Loop through the checkboxes.
						formElements.forEach( ( checkbox ) => {
							// Check if the value is present.
							if ( value === checkbox.getAttribute( 'value' ) ) {
								// Check the checkbox.
								checkbox.setAttribute( 'checked', 'checked' );
							}
						} );
					}
					break;

				case 'radio':
					// Get the radio buttons.
					formElements = form.querySelectorAll( `${ element }[name="fields\\[${ fieldName }\\]"]` ) as NodeListOf<HTMLInputElement>;

					// If radios are present.
					if ( formElements && formElements?.length ) {
						// Loop through the radios.
						formElements.forEach( ( radio ) => {
							// Check if the value is present.
							if ( value === radio.getAttribute( 'value' ) ) {
								// Check the radio.
								radio.setAttribute( 'checked', 'checked' );
							}
						} );
					}
					break;

				default:
					// Get the form element.
					formElements = form.querySelector( `${ element }[name="fields\\[${ fieldName }\\]"]` ) as HTMLSelectElement | HTMLInputElement;

					// Check if the form element exists.
					if ( formElements ) {
						// Set the value.
						formElements.value = value;
					}

					// If event is present in mapping, trigger the event.
					if ( event && formElements ) {
						// Trigger the event.
						formElements.dispatchEvent( new Event( mapping[ elementItem ].event ) );
					}
					break;
			}
		}
	} );
};
