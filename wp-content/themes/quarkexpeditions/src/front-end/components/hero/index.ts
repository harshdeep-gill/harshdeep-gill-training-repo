/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * External dependency
 */
import { TPFormFieldElement } from '@travelopia/web-components';

/**
 * HeroForm Class.
 */
export class HeroForm extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly openModalButton: HTMLButtonElement | null;
	private readonly fields: NodeListOf<TPFormFieldElement> | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.openModalButton = this.querySelector( 'quark-open-modal button' );
		this.fields = this.querySelectorAll( 'tp-form-field' );

		// Events.
		this.openModalButton?.addEventListener( 'click', this.modalButtonClicked.bind( this ) );
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

/**
 * Initialize
 */
customElements.define( 'quark-hero-form', HeroForm );
