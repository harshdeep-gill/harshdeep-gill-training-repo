/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * QuarkWistiaEmbed class
 */
export default class QuarkWistiaEmbed extends HTMLElement {
	/**
	 * Properties.
	 */
	private videoPlayer: WistiaVideo | null;
	private videoIntersectionObserver: IntersectionObserver | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialise super.
		super();

		// Get the overlay.
		this.videoPlayer = null;
		this.videoIntersectionObserver = null;
	}

	/**
	 * Connected Callback
	 */
	connectedCallback() {
		// Add initialization to queue
		window._wq = window._wq || [];
		const videoId = this.getAttribute( 'video-id' );

		// Push the video matcher to setup video player.
		window._wq.push( { id: videoId ?? '', onReady: this.setupVideoPlayer.bind( this ) } );

		// Get the wistia embed
		const wistiaEmbed = this.querySelector( '.wistia_embed' );

		// Default to 0px.
		let rootMarginTop = '0px';

		// Sticky header.
		const stickyHeader = document.querySelector( '.header, .lp-header' ) as HTMLElement;

		// Check if sticky header exists.
		if ( stickyHeader ) {
			rootMarginTop = `-${ getComputedStyle( stickyHeader ).height }`;
		}

		// Check if the embed is there.
		if ( wistiaEmbed ) {
			// Intersection observer.
			this.videoIntersectionObserver = new IntersectionObserver(
				this.intersectionObserverCallback.bind( this ),
				{
					root: document.body,
					threshold: 0,
					rootMargin: `${ rootMarginTop } 0px 0px 0px`,
				}
			);
			this.videoIntersectionObserver.observe( wistiaEmbed );
		}
	}

	/**
	 * Sets up the video player.
	 *
	 * @param { Object } videoPlayer
	 */
	setupVideoPlayer( videoPlayer: WistiaVideo ): void {
		// Assign video player.
		this.videoPlayer = videoPlayer;
	}

	/**
	 * Callback for intersection observer.
	 *
	 * @param { IntersectionObserverEntry[] } entries
	 */
	intersectionObserverCallback( entries: IntersectionObserverEntry[] ): void {
		// Loop through entries.
		entries.forEach( ( entry ) => {
			// Check if video is visible.
			if ( ! entry.isIntersecting ) {
				this.videoPlayer?.pause();
			}
		} );
	}
}
