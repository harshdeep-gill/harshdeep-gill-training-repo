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
	}

	/**
	 * Sets up the video player.
	 *
	 * @param { Object } videoPlayer
	 */
	setupVideoPlayer( videoPlayer: WistiaVideo ): void {
		// Assign video player.
		this.videoPlayer = videoPlayer;
		this.videoPlayer?.bind( 'pause', this.pause.bind( this ) );
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
	pause() {
		// Display the overlay.
		if ( this.overlay ) {
			this.overlay.style.display = 'flex';
		}

		// Display the thumbnail.
		if ( this.thumbnail ) {
			this.thumbnail.style.display = 'flex';
		}
	}
}

/**
 * Define the element.
 */
customElements.define( 'quark-video-icons-card', QuarkVideoIconsCardElement );
