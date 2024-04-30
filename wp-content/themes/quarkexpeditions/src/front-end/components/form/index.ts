/**
 * Internal dependencies
 */
import Form from './form';
import QuarkFileInput from './file-input';
import * as radio from './validators/radio';
import * as checkbox from './validators/checkbox';

/**
 * Validators.
 */
const validators = [
	radio,
	checkbox,
];

// Add all validations.
validators.forEach( (
	{ name, validator, errorMessage }: { name: string, validator: Object, errorMessage: string }
): void => {
	// Add validators to window
	window.tpFormValidators[ name ] = validator;
	window.tpFormErrors[ name ] = errorMessage;
} );

/**
 * Initialize.
 */
customElements.define( 'quark-form', Form );
customElements.define( 'quark-file-input', QuarkFileInput );
