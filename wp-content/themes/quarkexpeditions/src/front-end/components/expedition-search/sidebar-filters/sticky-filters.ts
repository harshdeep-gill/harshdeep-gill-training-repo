/**
 * Globals
 */
const { HTMLElement } = window;

/**
 * External dependencies
 */
import { TPModalElement } from '@travelopia/web-components';
import { QuarkModalOpenElement } from '../../modal/modal-open';
import { throttle } from '../../../global/utility';

/**
 * ExpeditionSearch Class.
 */
export default class ExpeditionSearchSidebarFilters extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly linkedModal: TPModalElement | null;
	private readonly modalOpener: QuarkModalOpenElement | null;
	private readonly searchWrapper: HTMLCollectionOf<Element>;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Initialize properties.
		this.modalOpener = this.querySelector( 'quark-modal-open' );
		this.linkedModal = null;
		this.searchWrapper = document.getElementsByTagName( 'quark-expedition-search' );

		// Check if we have modal opener
		if ( this.modalOpener ) {
			this.linkedModal = document.getElementById( this.modalOpener.getAttribute( 'modal-id' ) ?? '' ) as TPModalElement | null;

			// Do we have a modal?
			if ( this.linkedModal ) {
				window.addEventListener( 'resize', throttle( () => {
					// Check the window width
					if ( window.innerWidth > 1024 && this.linkedModal?.hasAttribute( 'open' ) ) {
						this.linkedModal?.removeAttribute( 'open' );
						document.body.classList.remove( 'prevent-scroll' );
					}
				} ) );
			}
		}

		// Check if we have search wrapper.
		if ( this.searchWrapper && this.searchWrapper.length > 0 ) {
			// Add Scroll event listener.
			document.addEventListener( 'scroll', throttle( () => {
				// Get end position of the search wrapper.
				const searchWrapperEndPosition = this.searchWrapper[ 0 ].getBoundingClientRect().bottom;

				// get position of the ExpeditionSearchSidebarFilters.
				const expeditionSearchSidebarFiltersPosition = this.getBoundingClientRect().top;

				// Check if the ExpeditionSearchSidebarFilters is at the bottom of the search wrapper.
				if ( expeditionSearchSidebarFiltersPosition >= searchWrapperEndPosition ) {
					this.style.visibility = 'hidden';
				} else {
					this.style.visibility = 'visible';
				}
			} ) );
		}

		// Append body.
		document.body.appendChild( this );
	}
}
