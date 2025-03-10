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
 * @param {HTMLFormElement}                        form    The form element.
 * @param {Record<string, Record<string, string>>} mapping The mapping of the form elements.
 */
export const prefillForm = ( form: HTMLFormElement, mapping: Record<string, Record<string, string>> ): void => {
	// Get the URL search params.
	const searchParams = new URLSearchParams( window.location.search );

	// Get the form elements.
	const formElements = Object.keys( mapping );

	// Loop through the form elements.
	formElements.forEach( ( elementItem ) => {
		// Get the value from the search params.
		const value = searchParams.get( elementItem );

		// Get the type, field name and event.
		const element = 'element' in mapping[ elementItem ] ? mapping[ elementItem ].element : 'input';
		const type = 'type' in mapping[ elementItem ] ? mapping[ elementItem ].type : '';
		const fieldName = 'fieldName' in mapping[ elementItem ] ? mapping[ elementItem ].fieldName : 'value';
		const event = 'event' in mapping[ elementItem ] ? mapping[ elementItem ].event : '';
		const dataAttribute = 'dataAttribute' in mapping[ elementItem ] ? mapping[ elementItem ].dataAttribute : false;

		// Check if the value exists.
		if ( value ) {
			// Initialize the form element.
			let formElement: HTMLSelectElement | NodeListOf<HTMLInputElement> | HTMLInputElement | null;

			// Switch based on the element type.
			switch ( type ) {
				// Checkbox element.
				case 'checkbox':
					formElement = form.querySelectorAll( `${ element }[name="fields\\[${ fieldName }\\]\\[\\]"]` ) as NodeListOf<HTMLInputElement>;
					const values = value.split( ';' );

					// If checkboxes are present.
					if ( formElement && formElement?.length ) {
						// Loop through the checkboxes.
						formElement.forEach( ( checkbox ) => {
							// Check data attribute.
							if ( dataAttribute ) {
								// Get the data attribute.
								const dataAttributeValue = checkbox.getAttribute( `data-${ dataAttribute }` );

								// Loop through the values.
								for ( const valueItem of values ) {
									// Check if the value is present.
									if ( dataAttributeValue?.includes( valueItem ) ) {
										// Check the checkbox.
										checkbox.setAttribute( 'checked', 'checked' );
									}
								}
							} else {
								// Loop through the values.
								for ( const valueItem of values ) {
									// Check if the value is present.
									if ( valueItem === checkbox.getAttribute( 'value' ) ) {
										// Check the checkbox.
										checkbox.setAttribute( 'checked', 'checked' );
									}
								}
							}
						} );
					}
					break;

				// Radio element.
				case 'radio':
					formElement = form.querySelectorAll( `${ element }[name="fields\\[${ fieldName }\\]"]` ) as NodeListOf<HTMLInputElement>;

					// If radios are present.
					if ( formElement && formElement?.length ) {
						// Loop through the radios.
						formElement.forEach( ( radio ) => {
							// Check if the value is present.
							if ( value === radio.getAttribute( 'value' ) ) {
								// Check the radio.
								radio.setAttribute( 'checked', 'checked' );
							}
						} );
					}
					break;

				// Select/Input element.
				default:
					formElement = form.querySelector( `${ element }[name="fields\\[${ fieldName }\\]"]` ) as HTMLSelectElement | HTMLInputElement;

					// Check if the form element exists.
					if ( formElement ) {
						// Set the value.
						formElement.value = value;
					}

					// If event is present in mapping, trigger the event.
					if ( event && formElement ) {
						// Trigger the event.
						formElement.dispatchEvent( new Event( mapping[ elementItem ].event ) );
					}
					break;
			}
		}
	} );
};
