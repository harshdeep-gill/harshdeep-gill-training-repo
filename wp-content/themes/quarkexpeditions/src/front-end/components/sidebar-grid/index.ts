/**
 * class Sidebar.
 */
class Sidebar extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly isStickySidebar: String | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Data.
		this.isStickySidebar = this.dataset.isSticky ?? '';

		// Set styles only if the screen width is more than 1024px.
		if ( window.screen.width > 1024 ) {
			this.setStyles();
		}
	}

	/**
	 * Compute and set styles.
	 */
	setStyles() {
		// Possible header elements.
		const headerSelectors = [ '.header', '.lp-header' ];

		// Initialize Header Height.
		let headerHeight = 0;

		// Loop through the header selectors.
		headerSelectors.forEach( ( selector ) => {
			// Get the header element.
			const headerEl = document.querySelector( selector );

			// If a header exists, get the height.
			if ( headerEl && ! headerHeight ) {
				headerHeight = headerEl?.getBoundingClientRect()?.height;
			}
		} );

		// Check if sidebar is sticky.
		if ( this.isStickySidebar ) {
			// Set top inset and height.
			this.style.top = `${ headerHeight }px`;
			this.style.height = `calc(100vh - ${ headerHeight }px)`;
		}
	}
}

/**
 * Initialize
 */
customElements.define( 'quark-sidebar', Sidebar );
