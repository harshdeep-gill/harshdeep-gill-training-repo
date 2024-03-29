/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Fancy Video Class.
 */
export default class FancyVideo extends HTMLElement {
	/**
	 * Properties.
	 */
	private playButton: HTMLButtonElement | null;
	private videoURL: string | null;
	private videoIframeEl: HTMLIFrameElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize Parent.
		super();

		// Elements.
		this.playButton = this.querySelector( '.fancy-video__play-btn' );
		this.videoIframeEl = this.querySelector( '.fancy-video__iframe' );
		this.videoURL = this.getAttribute( 'url' );

		// Events.
		this.playButton?.addEventListener( 'click', this.playVideo.bind( this ) );
	}

	/**
	 * Event: Play video.
	 */
	playVideo() {
		// Check if the video iframe element exists.
		if ( this.videoIframeEl ) {
			this.videoIframeEl.src = `${ this.videoURL }?autoplay=1`;
		}

		// Toggle the attribute.
		this.toggleAttribute( 'active' );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-fancy-video', FancyVideo );
