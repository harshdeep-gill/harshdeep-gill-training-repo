/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Class SearchFilterDestinationsOption.
 */
export class SearchFilterStickyElement extends HTMLElement {
	/**
	 * Properties.
	 */
	private searchButton: HTMLElement | null;
	private triggerButton: HTMLElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize super.
		super();

		// Elements.
		this.searchButton = document.querySelector( '.search-filters-bar__search-button' );
		this.triggerButton = this.querySelector( '.modal__modal-open > button' );

		// Instantiate IntersectionObserver.
		const observer = new window.IntersectionObserver( this.intersectionObserverCallback.bind( this ), {
			rootMargin: '-100px 0px 0px 0px',
		} );

		// Add the search button to the observer.
		if ( this.searchButton ) {
			observer.observe( this.searchButton );
		}
	}

	/**
	 * IntersectionObserver callback.
	 *
	 * @param {Array} entries Target elements entry that are intersecting the viewport.
	 */
	intersectionObserverCallback( entries: IntersectionObserverEntry[] ) {
		// Loop through entries.
		entries.forEach( ( entry ) => {
			// Check if intersecting.
			const isIntersecting = entry.intersectionRatio > 0;

			// Set 'show' attribute if not intersecting.
			if ( ! isIntersecting ) {
				this.triggerButton?.removeAttribute( 'disabled' );
				this.setAttribute( 'show', 'yes' );
			} else {
				this.removeAttribute( 'show' );
				this.triggerButton?.setAttribute( 'disabled', '' );
			}
		} );
	}
}

// Define Element.
customElements.define( 'quark-search-filters-bar-sticky-cta', SearchFilterStickyElement );
