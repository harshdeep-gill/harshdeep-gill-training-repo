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

		// Events.
		this.playButton?.addEventListener( 'click', this.playVideo.bind( this ) );
	}

	/**
	 * Event: Play video.
	 */
	playVideo() {
		// Check if the video iframe element exists.
		if ( this.videoElement instanceof HTMLIFrameElement ) {
			this.videoElement.src = `${ this.videoURL }?autoplay=1&enablejsapi=1`;
		} else {
			this.videoElement?.setAttribute( 'play', 'yes' );
		}

		// Toggle the attribute.
		this.toggleAttribute( 'active' );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-fancy-video', FancyVideo );
