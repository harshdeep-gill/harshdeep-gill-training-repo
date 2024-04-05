/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * HeroOverlay Class.
 */
export default class HeroOverlay extends HTMLElement {
	/**
	 * Connected callback.
	 */
	connectedCallback(): void {
		// Get the style string.
		const styleString = this.dataset.style ?? '';

		// Set the background color.
		this.setAttribute( 'style', styleString );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-hero-overlay', HeroOverlay );
