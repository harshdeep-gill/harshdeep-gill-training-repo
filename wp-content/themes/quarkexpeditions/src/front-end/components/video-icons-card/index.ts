/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * QuarkVideoIconsCardElement class
 */
export default class QuarkVideoIconsCardElement extends HTMLElement {
	/**
	 * Properties.
	 */
	private overlay: HTMLElement | null;
	private thumbnail: HTMLElement | null;
	private videoPlayer: WistiaVideo | null;
	private videoIntersectionObserver: IntersectionObserver | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialise super.
		super();

		// Get the overlay.
		this.overlay = this.querySelector( '.video-icons-card__overlay' );
		this.thumbnail = this.querySelector( '.video-icons-card__thumbnail' );
		this.videoPlayer = null;
		this.videoIntersectionObserver = null;
	}

	/**
	 * Connected Callback
	 */
	connectedCallback() {
		// Add initialization to queue
		window._wq = window._wq || [];
		const videoId = this.getAttribute( 'video_id' );

		// Push the video matcher to setup video player.
		window._wq.push( { id: videoId ?? '', onReady: this.setupVideoPlayer.bind( this ) } );

		// Setup events.
		this.overlay?.addEventListener( 'click', this.start.bind( this ) );

		// Default to 0px.
		let rootMarginTop = '0px';

		// Sticky header.
		const stickyHeader = document.querySelector( '.lp-header' ) as HTMLElement;

		// Check if sticky header exists.
		if ( stickyHeader ) {
			rootMarginTop = `-${ getComputedStyle( stickyHeader ).height }`;
		}

		// Intersection observer.
		this.videoIntersectionObserver = new IntersectionObserver(
			this.intersectionObserverCallback.bind( this ),
			{
				root: document.body,
				threshold: 0,
				rootMargin: `${ rootMarginTop } 0px 0px 0px`,
			}
		);
		this.videoIntersectionObserver.observe( this );
	}

	/**
	 * Sets up the video player.
	 *
	 * @param { Object } videoPlayer
	 */
	setupVideoPlayer( videoPlayer: WistiaVideo ): void {
		// Assign video player.
		this.videoPlayer = videoPlayer;
		this.videoPlayer?.bind( 'pause', this.onPause.bind( this ) );
	}

	/**
	 * Starts the video player.
	 */
	start() {
		// Hide the overlay.
		if ( this.overlay ) {
			this.overlay.style.display = 'none';
		}

		// Hide the thumbnail
		if ( this.thumbnail ) {
			this.thumbnail.style.display = 'none';
		}

		// Play the video.
		this.videoPlayer?.play();
	}

	/**
	 * Event: Runs on video pause.
	 */
	onPause() {
		// Display the overlay.
		if ( this.overlay ) {
			this.overlay.style.display = 'flex';
		}

		// Display the thumbnail.
		if ( this.thumbnail ) {
			this.thumbnail.style.display = 'flex';
		}
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

/**
 * Define the element.
 */
customElements.define( 'quark-video-icons-card', QuarkVideoIconsCardElement );
