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
		this.dispatchEvent( new CustomEvent( 'open' ) );

		// Removes scroll from body.
		document.querySelector( 'body' )?.classList?.add( 'prevent-scroll' );

		// Add animation.
		this?.classList.add( 'drawer--open' );
		this?.addEventListener( 'animationend',
			() => this?.classList.remove( 'drawer--open' ),
			{ once: true }
		);
	}

	/**
	 * Close the drawer.
	 */
	close(): void {
		// Remove and add classes for slide out animation.
		this.classList.add( 'drawer--close' );
		this.addEventListener( 'animationend', function() {
			// Slide out.
			this.classList.remove( 'drawer--close' );

			// Remove the open attribute.
			this.removeAttribute( 'open' );

			// Dispatch the close event.
			this.dispatchEvent( new CustomEvent( 'close' ) );

			// Enable body scroll again.
			document.querySelector( 'body' )?.classList?.remove( 'prevent-scroll' );
		}, { once: true } );
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
