/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * External dependency
 */
import { TPFormFieldElement } from '@travelopia/web-components';

/**
 * FormTwoStep Class.
 */
export default class FormTwoStep extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly modalOpenButton: HTMLButtonElement | null;
	private readonly fields: NodeListOf<TPFormFieldElement> | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.modalOpenButton = this.querySelector( 'quark-modal-open' );
		this.fields = this.querySelectorAll( 'tp-form-field' );

		// Events.
		this.modalOpenButton?.addEventListener( 'click', this.modalButtonClicked.bind( this ), { capture: true } );
	}

	/**
	 * Event: Modal button clicked.
	 *
	 * @param {Event} e Click event.
	 */
	modalButtonClicked( e: Event ): void {
		// Check if we have fields.
		if ( ! this.fields ) {
			// Bail early if we don't.
			return;
		}

		// Validate fields.
		let valid: boolean = true;
		this.fields.forEach( ( field: TPFormFieldElement ) => {
			// Check if field is valid.
			if ( ! field.validate() ) {
				valid = false;
			}
		} );

		// Check if validation was successful.
		if ( valid ) {
			// It was successful, bail.
			return;
		}

		// It was not! Stop propogation (don't open the modal) and exit.
		e.preventDefault();
		e.stopImmediatePropagation();
	}
}
