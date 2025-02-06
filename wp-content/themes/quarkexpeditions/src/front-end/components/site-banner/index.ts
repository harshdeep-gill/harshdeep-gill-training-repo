/**
 * External dependencies.
 */
const { customElements, HTMLElement } = window;

/**
 * class Site Banner.
 */
export default class QuarkSiteBannerElement extends HTMLElement {
	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Setup banner height property.
		document.querySelector<HTMLElement>( ':root' )?.style.setProperty( '--site-banner-height', this.clientHeight + 'px' );
	}
}

/**
 * Initialize
 */
customElements.define( 'quark-site-banner', QuarkSiteBannerElement );
