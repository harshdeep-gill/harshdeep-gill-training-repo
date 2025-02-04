/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * External dependencies
 */
import QuarkWistiaEmbed from '../wistia-embed';

/**
 * Fancy Video Class.
 */
export default class FancyVideo extends HTMLElement {
	/**
	 * Properties.
	 */
	private playButton: HTMLButtonElement | null;
	private videoURL: string | null;
	private videoElement: HTMLIFrameElement | QuarkWistiaEmbed | null;
	private player: YT.Player | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize Parent.
		super();

		// Elements.
		this.playButton = this.querySelector( '.fancy-video__play-btn' );
		this.videoElement = this.querySelector( '.fancy-video__video' );
		this.videoURL = this.getAttribute( 'url' );
		this.player = null;

		// Add enablejsapi param.
		if ( this.videoURL && ! this.videoURL.includes( 'enablejsapi=1' ) ) {
			const url: URL = new URL( this.videoURL );
			url.searchParams.set( 'enablejsapi', '1' );
			this.videoURL = url.toString();
			this.videoElement?.setAttribute( 'src', this.videoURL );
		}

		// Load Youtube Iframe API.
		this.loadYouTubeIframeAPI();

		// Events.
		this.playButton?.addEventListener( 'click', this.playVideo.bind( this ) );
	}

	/**
	 * Load the YouTube Iframe API.
	 */
	loadYouTubeIframeAPI() {
		// Check if YT player is already defined.
		if ( window.YT && window.YT.Player ) {
			this.initPlayer();
		} else {
			// Wait for the existing script to trigger the API ready callback.
			const existingCallback = window.onYouTubeIframeAPIReady;

			// Set a new callback that ensures that the player is initialized.
			window.onYouTubeIframeAPIReady = () => {
				// Check if callback exists.
				if ( existingCallback ) {
					existingCallback();
				}

				// Initialize YouTube Player.
				this.initPlayer();
			};
		}
	}

	/**
	 * Initialize YouTube Player.
	 */
	initPlayer() {
		//
		if ( this.videoElement ) {
			// Check if video id exists, if not then set ID.
			if ( ! this.videoElement.id ) {
				this.videoElement.id = `fancy-video__video-${ Math.random().toString( 36 ).substring( 2, 9 ) }`;
			}

			// Get Video ID.
			const videoId = this.extractVideoId( this.videoURL );

			// If video id exists, initialize player object.
			if ( videoId ) {
				// Initialize new YT Player object.
				this.player = new window.YT.Player( this.videoElement.id, {
					height: '600',
					width: '1200',
					videoId,
					events: {
						onReady: this.playVideo,
					},
				} );
			}
		}
	}

	/**
	 * Extract YouTube Video ID from URL.
	 *
	 * @param {string | null } url YouTube Video Embed URL.
	 */
	extractVideoId( url: string | null ): string | null {
		// Check if url exists.
		if ( ! url ) {
			// Bail.
			return null;
		}

		// YouTube regex to handle both watch and embed URLs.
		const regex = /(?:youtube\.com\/(?:[^\/]+\/?(?:v\/)?|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
		const match = url.match( regex );

		// Return the video id.
		return match ? match[ 1 ] : null;
	}

	/**
	 * Event: Play video.
	 */
	playVideo() {
		// Check if the video iframe element exists.
		if ( this.player ) {
			this.player.playVideo();
		}

		// Toggle the attribute.
		this.toggleAttribute( 'active' );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-fancy-video', FancyVideo );
