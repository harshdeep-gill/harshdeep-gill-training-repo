/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * External dependency.
 */
import '@travelopia/web-components/dist/accordion';

/**
 * Internal dependency.
 */
import { TPAccordionItemElement } from '@travelopia/web-components';

/**
 * Accordion Class.
 */
export default class Accordion extends HTMLElement {
	/**
	 * Connected Callback.
	 */
	connectedCallback(): void {
		// Open accordion by hash on initial render.
		this.openAccordionByHashInUrl();

		// Event.
		window.addEventListener( 'hashchange', () => this.openAccordionByHashInUrl() );
	}

	/**
	 * Open accordion by hash in url
	 */
	openAccordionByHashInUrl(): void {
		// Get the hash.
		const hash = window.location.hash;

		// Check if hash is not available, return.
		if ( ! hash ) {
			// Hash not found, bail early.
			return;
		}

		// Get the accordion item.
		const accordionItem: TPAccordionItemElement | null = this.querySelector( `tp-accordion-item${ hash }` );

		// Check if accordion item is not available, return.
		if ( ! accordionItem ) {
			// Accordion item not found, bail early.
			return;
		}

		// Open the accordion item that is in hash.
		accordionItem.open();
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-accordion', Accordion );
