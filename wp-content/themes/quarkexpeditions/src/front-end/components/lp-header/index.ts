/**
 * class LPHeader.
 */
class LPHeader extends HTMLElement {
	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Events
		document.body.addEventListener( 'scroll', this.onBodyScroll.bind( this ) );
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
customElements.define( 'quark-lp-header', LPHeader );
