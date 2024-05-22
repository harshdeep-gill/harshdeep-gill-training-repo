/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Internal dependencies.
 */
import { slideElementUp, slideElementDown } from '../../global/utility';

/**
 * Footer Dropdown Class.
 */
class FooterAccordion extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly content: HTMLElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		const titleHandle = this.querySelector( '.footer__column-title' );
		this.content = this.querySelector( '.footer__nav' );

		// Events.
		titleHandle?.addEventListener( 'click', this.buttonClicked.bind( this ) );
	}

	/**
	 * Event: Button clicked.
	 */
	buttonClicked() {
		// Check if content exists.
		if ( ! this.content ) {
			// Return if content does not exits.
			return;
		}

		// Toggle attribute.
		this.toggleAttribute( 'active' );

		// Toggle content.
		if ( ! this.hasAttribute( 'active' ) ) {
			slideElementUp( this.content, 600 );
		} else {
			slideElementDown( this.content, 600 );
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-footer-accordion', FooterAccordion );
