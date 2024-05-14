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
	private levelTwoHeadings: NodeListOf<Element> | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.allContentItems = this.querySelectorAll( '.table-of-contents__list-item' );
		this.levelTwoHeadings = document.querySelectorAll( '.sidebar-grid__content h2' );

		// Highlight content item by hash on initial render.
		this.highlightContentItemByHash();

		// Initialize IntersectionObserver to highlight item on scroll.
		this.highlightItemOnScroll();
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
		if ( contentItem ) {
			// Set content item as active.
			this.setItemAsActive( contentItem );
		}
	}

	/**
	 * Set the passed item as active.
	 *
	 * @param {HTMLElement | Element} itemEl Content Item to be set as active.
	 */
	setItemAsActive( itemEl: HTMLElement | Element ) {
		// Check if item exists.
		if ( ! itemEl ) {
			// Bail early.
			return;
		}

		// Check if the passed element has the expected class.
		if ( ! itemEl.classList.contains( 'table-of-contents__list-item' ) ) {
			// If not exists, bail early.
			return;
		}

		// Loop through all content items.
		if ( this.allContentItems ) {
			this.allContentItems.forEach( ( item ) => {
				// Remove any existing active class from other items.
				item.classList.remove( 'table-of-contents__list-item--active' );
			} );
		}

		// Add class to the current active content item.
		itemEl.classList.add( 'table-of-contents__list-item--active' );
	}

	/**
	 * Highlight content item on scroll.
	 */
	highlightItemOnScroll() {
		// Get the header height.
		const headerHeight = getComputedStyle( document.body )?.getPropertyValue( '--header-height' ) ?? 0;

		// Calculate the top root margin.
		const rootMarginTop = ( parseInt( headerHeight ) + 24 ) + 'px';

		// Instantiate IntersectionObserver.
		const observer = new IntersectionObserver(
			this.intersectionObserverCallback.bind( this ),
			{
				rootMargin: `${ rootMarginTop } 0px -75% 0px`,
				threshold: 1,
			} );

		// Loop thorugh all headings.
		if ( this.levelTwoHeadings ) {
			this.levelTwoHeadings?.forEach( ( heading ) => {
				// Observe each heading.
				observer.observe( heading );
			} );
		}
	}

	/**
	 * Intersection Observer Callback.
	 *
	 * @param {IntersectionObserverEntry[]} entries Observed Entries.
	 */
	intersectionObserverCallback( entries: IntersectionObserverEntry[] ) {
		// Loop thorugh entries.
		entries.forEach( ( entry ) => {
			// Get value for isIntersecting.
			const isActive = entry.isIntersecting;

			// Get the heading id from current entry.
			const headingId = entry.target.id;

			// Get the content item.
			if ( headingId ) {
				const contentItem = this.querySelector( `.table-of-contents__list-item[data-anchor="#${ headingId }"]` );

				// If no content item exists.
				if ( ! contentItem ) {
					// Bail early.
					return;
				}

				// Check if content item is not available, return.
				if ( isActive ) {
					// Set content item as active.
					this.setItemAsActive( contentItem );
				}
			}
		} );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-table-of-contents', TableOfContents );
