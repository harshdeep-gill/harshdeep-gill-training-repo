/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * TableOfContents Class.
 */
export default class TableOfContents extends HTMLElement {
	/**
	 * Properties.
	 */
	private allContentItems: NodeListOf<Element> | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.allContentItems = this.querySelectorAll( '.table-of-contents__list-item' );

		// Highlight content item by hash on initial render.
		this.highlightContentItemByHash();

		// Event.
		window.addEventListener( 'hashchange', () => this.highlightContentItemByHash() );
	}

	/**
	 * Highlight content item by hash in url
	 */
	highlightContentItemByHash(): void {
		// Get the hash.
		const hash = window.location.hash;

		// Check if hash is not available, return.
		if ( ! hash ) {
			// Hash not found, bail early.
			return;
		}

		// Get the content item.
		const contentItem = this.querySelector( `.table-of-contents__list-item[data-anchor="${ hash }"]` );

		// Check if content item is not available, return.
		if ( ! contentItem ) {
			// Content item not found, bail early.
			return;
		}

		// Loop through all content items.
		if ( this.allContentItems ) {
			this.allContentItems.forEach( ( item ) => {
				// Remove existing active class.
				item.classList.remove( 'table-of-contents__list-item--active' );
			} );
		}

		// Add class to the active content item.
		contentItem.classList.add( 'table-of-contents__list-item--active' );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-table-of-contents', TableOfContents );
