/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * External dependencies.
 */
import { TPLightboxTriggerElement } from '@travelopia/web-components';

/**
 * MediaLightbox Class.
 */
export default class MediaLightbox extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly triggerElement: TPLightboxTriggerElement | null;
	private readonly triggerButton: HTMLButtonElement | null | undefined;
	private readonly dialogElement: HTMLDialogElement | undefined;

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

		// Get lightbox id
		const lightboxID = this.triggerElement.getAttribute( 'lightbox' ) ?? '';

		// Check for lightboxID
		if ( lightboxID ) {
			// Get the dialog element.
			this.dialogElement = document.querySelector( `#${ lightboxID } dialog` ) as HTMLDialogElement ?? undefined;

			// Setup close event.
			this.dialogElement?.addEventListener( 'close', this.handleModalClose.bind( this ) );
		}

		// Events.
		this.triggerButton?.addEventListener( 'click', this.handleTriggerClick.bind( this ) );
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
	 * Handles the modal close event.
	 */
	handleModalClose(): void {
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
}

/**
 * Initialize.
 */
customElements.define( 'quark-media-lightbox', MediaLightbox );
