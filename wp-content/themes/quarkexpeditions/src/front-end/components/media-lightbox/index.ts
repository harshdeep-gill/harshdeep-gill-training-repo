/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * External dependencies.
 */
import { TPLightboxElement, TPLightboxTriggerElement } from '@travelopia/web-components';

/**
 * QuarkMediaLightbox Class.
 */
export default class QuarkMediaLightbox extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly lightbox: TPLightboxElement | null | undefined;
	private readonly triggerElement: TPLightboxTriggerElement | null;
	private readonly triggerButton: HTMLButtonElement | null | undefined;
	private readonly dialogElement: HTMLDialogElement | undefined;
	private readonly bulletElement: HTMLElement | undefined;
	private readonly nextButtonElement: HTMLElement | undefined;
	private readonly prevButtonElement: HTMLElement | undefined;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Get trigger element.
		this.triggerElement = this.querySelector( 'tp-lightbox-trigger' );

		// Do we have triggerElement?
		if ( ! this.triggerElement ) {
			// No, we don't.
			return;
		}

		// Get trigger button.
		this.triggerButton = this.triggerElement.querySelector( 'button' );

		// Events.
		this.triggerButton?.addEventListener( 'click', this.handleTriggerClick.bind( this ) );

		// If the lightbox is the child of this element, we should take it out as a child of the body.
		const lightboxChild = this.querySelector( 'tp-lightbox' );

		// Is it there?
		if ( lightboxChild ) {
			// Yes, take it out.
			document.body.appendChild( lightboxChild );
		}

		// Get lightbox id
		const lightboxID = this.triggerElement.getAttribute( 'lightbox' ) ?? '';

		// Check for lightboxID
		if ( ! lightboxID ) {
			// Not found, bail.
			return;
		}

		// Initialize elements.
		this.lightbox = document.querySelector( `#${ lightboxID }` ) as TPLightboxElement;

		// Check if we have a lightbox.
		if ( ! this.lightbox ) {
			// We don't, bail.
			return;
		}

		// Initialize elements.
		this.bulletElement = this.lightbox.querySelector( '.media-lightbox__bullets' ) as HTMLElement;
		this.nextButtonElement = this.lightbox.querySelector( 'tp-lightbox-next' ) as HTMLElement;
		this.prevButtonElement = this.lightbox.querySelector( 'tp-lightbox-previous' ) as HTMLElement;
		this.dialogElement = this.lightbox.querySelector( 'dialog' ) as HTMLDialogElement;

		// Check if the lightbox has template-set event listener.
		const hasTemplateChangeEventListener = this.lightbox.getAttribute( 'data-event-added' );

		// Events
		if ( ! hasTemplateChangeEventListener ) {
			this.lightbox.addEventListener( 'template-set', this.addBulletsToLightbox.bind( this ) );
			this.lightbox.setAttribute( 'data-event-added', 'yes' );
		}

		// Setup close event handler.
		this.dialogElement?.addEventListener( 'close', this.handleLightboxClose.bind( this ) );
	}

	/**
	 * Handles the click on the lightbox trigger.
	 */
	handleTriggerClick(): void {
		// Get the iframe.
		const videoIframe = this.querySelector( 'template' )?.content.querySelector( 'iframe' );

		// Check if we have iframe.
		if ( ! videoIframe ) {
			// We don't.
			return;
		}

		// Set autoplay.
		videoIframe.src = `${ videoIframe.dataset.path }?autoplay=1`;
	}

	/**
	 * Handles the lightbox close event.
	 */
	handleLightboxClose(): void {
		// Get the iframe.
		const videoIframe = this.dialogElement?.querySelector( 'iframe' );

		// Check if we have iframe.
		if ( ! videoIframe ) {
			// We don't.
			return;
		}

		// Unset autoplay.
		videoIframe.src = `${ videoIframe.dataset.path }`;
	}

	/**
	 * Adds dots slider to the lightbox.
	 */
	addBulletsToLightbox() {
		// Check if lightbox is available.
		if ( ! this.lightbox ) {
			// If lightbox does not exist, bail out.
			return;
		}

		// Get the current and total slide number.
		const current: string = this.lightbox.currentIndex.toString() ?? '';
		const total: string = this.lightbox.getAttribute( 'total' ) ?? '';

		// Check if the total number of slides is less than or equal to 1.
		if ( 1 >= parseInt( total ) ) {
			// Set the attribute hidden to next and previous buttons.
			this.nextButtonElement?.setAttribute( 'hidden', 'true' );
			this.prevButtonElement?.setAttribute( 'hidden', 'true' );

			// If the total number of slides is less than or equal to 1, bail out.
			return;
		}

		// Check if the bullet container exists.
		if ( ! this.bulletElement ) {
			// If bullet container does not exist, bail out.
			return;
		}

		// Clear the bullet container.
		this.bulletElement.innerHTML = '';

		// Loop to create the specified number of bullets.
		for ( let i = 0; i < parseInt( total ); i++ ) {
			// Create a new button element for each bullet.
			const singleBullet: HTMLElement = document.createElement( 'button' );

			// Add an event listener to the bullet.
			singleBullet.addEventListener( 'click', () => {
				// Set the current slide to the bullet index.
				if ( this.lightbox ) {
					this.lightbox.currentIndex = i + 1;
				}
			} );

			// Add the appropriate class to the bullet.
			singleBullet.classList.add( 'media-lightbox__bullet' );

			// If this bullet is the current one, mark it accordingly.
			if ( i + 1 === parseInt( current ) ) {
				singleBullet.setAttribute( 'current', 'yes' );
			}

			// Append the bullet to the container.
			this.bulletElement.appendChild( singleBullet );
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-media-lightbox', QuarkMediaLightbox );
