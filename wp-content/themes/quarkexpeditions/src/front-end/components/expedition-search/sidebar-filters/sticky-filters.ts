/**
 * Globals
 */
const { HTMLElement } = window;

/**
 * External dependencies
 */
import { TPModalElement } from '@travelopia/web-components';
import { QuarkModalOpenElement } from '../../modal/modal-open';

/**
 * ExpeditionSearch Class.
 */
export default class ExpeditionSearchSidebarFilters extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly linkedModal: TPModalElement | null;
	private readonly modalOpener: QuarkModalOpenElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Initialize properties.
		this.modalOpener = this.querySelector( 'quark-modal-open' );
		this.linkedModal = null;

		// Check if we have modal opener
		if ( this.modalOpener ) {
			this.linkedModal = document.getElementById( this.modalOpener.getAttribute( 'modal-id' ) ?? '' ) as TPModalElement | null;

			// Do we have a modal?
			if ( this.linkedModal ) {
				window.addEventListener( 'resize', () => {
					// Check the window width
					if ( window.innerWidth > 1024 && this.linkedModal?.hasAttribute( 'open' ) ) {
						this.linkedModal?.removeAttribute( 'open' );
						document.body.classList.remove( 'prevent-scroll' );
					}
				} );
			}
		}

		// Append body.
		document.body.appendChild( this );
	}
}
