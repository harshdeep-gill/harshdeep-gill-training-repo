/**
 * Global variables.
 */
const { customElements, HTMLElement, GLightbox } = window;

/**
 * MediaLightbox Class.
 */
export default class MediaLightbox extends HTMLElement {
	/**
	 * Properties.
	 */
	public lightbox: typeof GLightbox | undefined;
	private readonly slideIndexElement: HTMLDivElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Create slide index element.
		this.slideIndexElement = document.createElement( 'div' );
		this.slideIndexElement.classList.add( 'glightbox-slide-index' );

		// Initialize lightbox.
		this.initialize();
	}

	/**
	 * Initialize lightbox.
	 */
	initialize(): void {
		// Check if GLightbox exists.
		if ( ! GLightbox ) {
			// Bail early.
			return;
		}

		// Initialize the lightbox.
		this.lightbox = new GLightbox( {
			touchNavigation: true,
			loop: false,
			openEffect: 'fade',
			closeEffect: 'fade',
			autoplayVideos: true,
			videosWidth: '1280px',
			onClose: () => this.clearSlideIndexText(),
			afterSlideChange: ( _prev: any, next: { index: any } ) => {
				// Check if next and slide index element is available.
				if ( next && this.slideIndexElement ) {
					// Add the slide count in the text.
					this.slideIndexElement.innerText = `${ next.index + 1 } of ${ this.lightbox?.elements.length }`;
				}
			},
		} );

		// Add captions and slide indexes.
		this.addSlideIndex();
	}

	/**
	 * Add slide index.
	 */
	addSlideIndex() {
		// Prepare slider counter on 'open' event.
		this.lightbox?.on( 'open', () => {
			// If slideIndexElement is not present return.
			if ( ! this.slideIndexElement ) {
				// Return.
				return;
			}

			// Add slide index element inside slide container.
			this.lightbox?.slidesContainer.after( this.slideIndexElement );

			// Events: Immediately hide the counter when the close button is click or the overlay is clicked.
			this.lightbox?.modal?.querySelector( '.gclose.gbtn' )?.addEventListener( 'click', this.clearSlideIndexText.bind( this ) );
			this.lightbox?.slidesContainer?.addEventListener( 'click', this.clearSlideIndexText.bind( this ) );
		} );
	}

	/**
	 * Clear slide index text.
	 */
	clearSlideIndexText() {
		// If slide index element is not available return.
		if ( ! this.slideIndexElement ) {
			// Early return.
			return;
		}

		// Set the slide index element text to empty.
		this.slideIndexElement.innerText = '';
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-media-lightbox', MediaLightbox );
