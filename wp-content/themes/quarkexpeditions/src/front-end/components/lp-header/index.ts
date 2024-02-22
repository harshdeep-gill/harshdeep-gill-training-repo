/**
 * class LPHeader.
 */
class LPHeader extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly heroImmersiveElement: HTMLElement | null;
	private readonly observer: IntersectionObserver | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.heroImmersiveElement = document.querySelector( '.hero--immersive' );

		// IntersectionObserver options.
		const options = {
			threshold: 0.85,
		};

		// Instantiate IntersectionObserver.
		this.observer = new IntersectionObserver( this.observeHeaderCallback.bind( this ), options );

		// Observe the Immersive Hero Element.
		if ( this.heroImmersiveElement ) {
			this.observer.observe( this.heroImmersiveElement );
		}
	}

	/**
	 * Observe Header Element.
	 *
	 * @param {IntersectionObserverEntry[]} entries Observer Entries.
	 */
	observeHeaderCallback( entries: IntersectionObserverEntry[] | null ) : void {
		// Check if entries exist.
		if ( ! entries ) {
			// Bail if no entries.
			return;
		}

		// Loop through entries.
		entries.forEach( ( entry: IntersectionObserverEntry ) => {
			// Get isIntersecting value.
			const isIntersecting = entry?.isIntersecting;

			// Check if Hero element exists and is the next sibling element of header.
			if (
				this.heroImmersiveElement &&
				this.heroImmersiveElement === this.nextElementSibling
			) {
				// Check if hero element is intersecting, then add/remove class.
				if ( ! isIntersecting ) {
					this.classList.add( 'lp-header--compact' );
				} else {
					this.classList.remove( 'lp-header--compact' );
				}
			}
		} );
	}
}

/**
 * Initialize
 */
customElements.define( 'quark-lp-header', LPHeader );
