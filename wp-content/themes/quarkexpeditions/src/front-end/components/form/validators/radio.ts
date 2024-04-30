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
export const name: string = 'radio';

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
		// Initialize isRadioInputValid valid.
		let isRadioInputValid: boolean = false;

		// Get all radio input elements inside this form field.
		const radioInputElements: NodeListOf<HTMLInputElement> = field.querySelectorAll( 'input[type="radio"]' );

		// Loop through radio input elements
		radioInputElements?.forEach( ( radioElement: HTMLInputElement ): void => {
			// If at least one radio element is checked, set validity to true.
			if ( radioElement.checked ) {
				isRadioInputValid = true;
			}
		} );

		// Return the value of isRadioInputValid.
		return isRadioInputValid;
	},

	/**
	 * Get error message.
	 */
	getErrorMessage: (): string => getErrorMessage( name ),
};
