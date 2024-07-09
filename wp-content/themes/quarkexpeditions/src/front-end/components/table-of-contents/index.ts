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

		// Set heading ids.
		this.setHeadingIDs();

		// Highlight content item by hash on initial render.
		this.highlightContentItemByHash();

		// Initialize IntersectionObserver to highlight item on scroll.
		this.highlightItemOnScroll();
	}

	/**
	 * Set the heading IDs
	 */
	setHeadingIDs(): void {
		// Check if headings and items are present.
		if ( ! this.levelTwoHeadings || ! this.allContentItems ) {
			// No, bail.
			return;
		}

		// Get the headings and items as arrays.
		const levelTwoHeadings = [ ...this.levelTwoHeadings ] as HTMLElement[];
		const listContentItems = [ ...this.allContentItems ] as HTMLElement[];

		// Loop through and set the IDs.
		for ( let idx = 0; idx < listContentItems.length; idx++ ) {
			const theID = listContentItems[ idx ].dataset.anchor?.substring( 1 ) ?? '';

			// Check if the heading exists.
			if ( idx >= levelTwoHeadings.length ) {
				break;
			}

			// Set the ID.
			levelTwoHeadings[ idx ].id = theID;
		}
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
	 * @param {HTMLElement | Element} itemElement Content Item to be set as active.
	 */
	setItemAsActive( itemElement: HTMLElement | Element ) {
		// Check if item exists.
		if ( ! itemElement ) {
			// Bail early.
			return;
		}

		// Check if the passed element has the expected class.
		if ( ! itemElement.classList.contains( 'table-of-contents__list-item' ) ) {
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
		itemElement.classList.add( 'table-of-contents__list-item--active' );
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
