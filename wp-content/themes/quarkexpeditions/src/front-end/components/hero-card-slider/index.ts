/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * External dependency
 */
import { TPSliderElement, TPSliderSlideElement } from '@travelopia/web-components';

/**
 * HeroCardSlider Class.
 */
export default class HeroCardSlider extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly slider: TPSliderElement | null;
	private readonly cards: NodeListOf<TPSliderSlideElement> | null;
	private readonly cardsIntersectionObserver: IntersectionObserver;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.slider = this.querySelector( '.hero-card-slider' );
		this.cards = this.querySelectorAll( '.hero-card-slider__card' );

		// Setup intersection observer.
		this.cardsIntersectionObserver = new IntersectionObserver(
			this.intersectionObserverCallback.bind( this ),
			{
				root: this.slider,
				threshold: 0.05,
			}
		);

		// Events.
		this.cards.forEach( ( card ) => {
			// Set events for cards.
			card.addEventListener( 'mouseover', this.handleMouseOver.bind( this ) );
			card.addEventListener( 'mouseout', this.handleMouseOut.bind( this ) );
			this.cardsIntersectionObserver.observe( card );
		} );
	}

	/**
	 * Handle the mouse over event.
	 */
	handleMouseOver(): void {
		// Disable the slider.
		this.slider?.setAttribute( 'disabled', 'yes' );

		// Find the active card.
		const activeCard = this.querySelector( '.hero-card-slider__card[active="yes"]' );

		// Check if active card found.
		if ( ! activeCard ) {
			// Not found. Bail.
			return;
		}

		// Get the possible video child.
		const maybeVideo = activeCard.querySelector( 'video' );

		// Play the video.
		this.playVideo( maybeVideo );
	}

	/**
	 * Handles the mouse out event.
	 */
	handleMouseOut(): void {
		// Find the active card.
		const activeCard = this.querySelector( '.hero-card-slider__card[active="yes"]' );

		// Check if active card found.
		if ( ! activeCard ) {
			// Not found. Bail.
			return;
		}

		// Get the possible video child.
		const maybeVideo = activeCard.querySelector( 'video' );

		// Pause the video.
		this.pauseVideo( maybeVideo );

		// Enable the slider.
		this.slider?.removeAttribute( 'disabled' );
	}

	/**
	 * Pause a video.
	 *
	 * @param { HTMLVideoElement | null } maybeVideo
	 */
	pauseVideo( maybeVideo: HTMLVideoElement | null ): void {
		// There is a video.
		if ( maybeVideo && maybeVideo instanceof HTMLVideoElement ) {
			maybeVideo.muted = true;
			maybeVideo.pause();
		}
	}

	/**
	 * Play a video.
	 *
	 * @param { HTMLVideoElement | null } maybeVideo
	 */
	playVideo( maybeVideo: HTMLVideoElement | null ): void {
		// There is a video.
		if ( maybeVideo && maybeVideo instanceof HTMLVideoElement ) {
			maybeVideo.play();
			maybeVideo.muted = false;
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
				const maybeVideo = entry.target.querySelector( 'video' );
				this.pauseVideo( maybeVideo );
			}
		} );
	}
}

// Define element.
customElements.define( 'quark-hero-card-slider', HeroCardSlider );
