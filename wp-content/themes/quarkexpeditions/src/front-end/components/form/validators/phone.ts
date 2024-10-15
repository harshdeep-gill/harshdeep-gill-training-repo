/**
 * External dependencies.
 */
import { TPFormFieldElement } from '@travelopia/web-components';

/**
 * Internal dependencies.
 */
import { getErrorMessage } from '../utility';

/**
 * Name.
 */
export const name: string = 'phone-field-validation';

/**
 * Error message.
 */
export const errorMessage: string = 'Please enter a valid phone number.';

/**
 * Validator.
 */
export const validator = {
	/**
	 * Validate
	 *
	 * @param {Object} field Field.
	 */
	validate: ( field: TPFormFieldElement ) => {
		// Initialize isPhoneInputValid valid.
		let isPhoneInputValid: boolean = false;

		// Get phone input element inside this form field.
		const phoneInputElement: HTMLInputElement | null = field.querySelector( 'input[type="tel"]' );

		// Regular expression to allow digits, spaces, and specific characters: + - ( )
		const phoneRegex = /^[\d+\-()\s]+$/;

		// Check if phone input element is found and its value matches the regex.
		if ( phoneInputElement && phoneRegex.test( phoneInputElement.value.trim() ) ) {
			isPhoneInputValid = true;
		}

		// Return the value of isPhoneInputValid.
		return isPhoneInputValid;
	},

	/**
	 * Get error message.
	 */
	getErrorMessage: (): string => getErrorMessage( name ),
};
