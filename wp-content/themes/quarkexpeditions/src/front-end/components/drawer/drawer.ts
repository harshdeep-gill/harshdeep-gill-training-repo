/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * QuarkDrawerElement Class.
 */
export class QuarkDrawerElement extends HTMLElement {
	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Move drawer as a direct descendent of body to avoid z-index issues.
		document.querySelector( 'body' )?.appendChild( this );
	}

	/**
	 * Connected callback.
	 */
	connectedCallback() {
		// Event listeners.
		this.addEventListener( 'click', this.handleClick.bind( this ) );
	}

	/**
	 * Opens the drawer.
	 */
	open(): void {
		// Set the attribute
		this.setAttribute( 'open', 'yes' );

		// Dispatch open event.
		this.dispatchEvent( new CustomEvent( 'open', { bubbles: true } ) );

		// Removes scroll from body.
		document.querySelector( 'body' )?.classList?.add( 'prevent-scroll' );
	}

	/**
	 * Close the drawer.
	 */
	close(): void {
		// Remove the open attribute.
		this.removeAttribute( 'open' );

		// Dispatch the close event.
		this.dispatchEvent( new CustomEvent( 'close', { bubbles: true } ) );

		// Enable body scroll again.
		document.querySelector( 'body' )?.classList?.remove( 'prevent-scroll' );
	}

	/**
	 * Handle when the component is clicked.
	 *
	 * @param {Event} e Event.
	 */
	handleClick( e: Event ): void {
		// Check whether the target was this element.
		if ( e.target === this ) {
			e.preventDefault();
			e.stopPropagation();
			this.close();
		}
	}
}
