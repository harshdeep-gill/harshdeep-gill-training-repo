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

	/**
	 * Event: Body Scroll.
	 */
	onBodyScroll() : void {
		// Check if entries exist.
		if ( 50 < document.body.scrollTop ) {
			this.classList.add( 'lp-header--compact' );
		} else if ( ! document.body.scrollTop ) {
			this.classList.remove( 'lp-header--compact' );
		}
	}
}

/**
 * Initialize
 */
customElements.define( 'quark-site-banner', QuarkSiteBannerElement );
