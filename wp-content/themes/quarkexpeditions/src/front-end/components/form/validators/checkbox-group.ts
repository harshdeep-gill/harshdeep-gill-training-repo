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
export const name: string = 'checkbox-group-required';

/**
 * Error message.
 */
export const errorMessage: string = 'Please make a selection to continue';

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

		// Get all checkbox input elements inside this form field.
		const checkboxInputElements: NodeListOf<HTMLInputElement> = field.querySelectorAll( 'input[type="checkbox"]' );

		// Loop through checkbox input elements
		checkboxInputElements?.forEach( ( checkboxElement: HTMLInputElement ): void => {
			// If at least one checkbox element is checked, set validity to true.
			if ( checkboxElement.checked ) {
				isCheckboxInputValid = true;
			}
		} );

		// Return the value of isCheckboxInputValid.
		return isCheckboxInputValid;
	},

	/**
	 * Get error message.
	 */
	getErrorMessage: (): string => getErrorMessage( name ),
};
