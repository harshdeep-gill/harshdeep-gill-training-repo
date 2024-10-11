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
export const name: string = 'file-size-valid';

/**
 * Error message.
 */
export const errorMessage: string = 'File size exceeds the allowed limit.';

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
		// Initialize isFileSizeValid valid.
		let isFileSizeValid: boolean = false;

		// Get the file info.
		const fileInput: HTMLElement | null = field.querySelector( 'quark-file-input' );
		const fileInputElement: HTMLInputElement | null = field.querySelector( 'input[type="file"]' );
		const file = fileInputElement?.files?.item( 0 );
		const allowedFileSize = parseFloat( fileInput?.getAttribute( 'allowed_file_size' ) ?? '8' );

		// Check if the file exists.
		if ( ! file || ! allowedFileSize ) {
			// Bail.
			return;
		}

		// Check if the file size exceeds the allowed size.
		if ( file.size <= ( allowedFileSize * ( 1024 ** 2 ) ) ) {
			// Assign true.
			isFileSizeValid = true;
		}

		// Return the value of isFileSizeValid.
		return isFileSizeValid;
	},

	/**
	 * Get error message.
	 */
	getErrorMessage: (): string => getErrorMessage( name ),
};
