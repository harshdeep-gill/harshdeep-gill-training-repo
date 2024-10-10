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
export const name: string = 'checkbox-field-required';

/**
 * Error message.
 */
export const errorMessage: string = 'Please tick this box if you want to proceed.';

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
		// Initialize isCheckboxInputValid valid.
		let isCheckboxInputValid: boolean = false;

		// Get checkbox input element inside this form field.
		const checkboxInputElement: HTMLInputElement | null = field.querySelector( 'input[type="checkbox"]' );

		// Check if checkbox input element is found and checked.
		if ( checkboxInputElement && checkboxInputElement.checked ) {
			isCheckboxInputValid = true;
		}

		// Return the value of isCheckboxInputValid.
		return isCheckboxInputValid;
	},

	/**
	 * Get error message.
	 */
	getErrorMessage: (): string => getErrorMessage( name ),
};
