/**
 * External Dependencies.
 */
import '@travelopia/web-components/dist/modal';
import { TPModalElement } from '@travelopia/web-components';

/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * QuarkModalOpenElement Class.
 */
export class QuarkModalOpenElement extends HTMLElement {
	/**
	 * Properties.
	 */
	private modal: TPModalElement | null | undefined;
	private modalId: string | null;
	private modalResizeObserver: ResizeObserver | undefined;
	private modalContentElement: HTMLElement | null | undefined;
	private modalBodyElement: HTMLElement | null | undefined;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.modalId = this.getAttribute( 'modal-id' );

		// Check if modal id is not available, return.
		if ( ! this.modalId ) {
			// Modal ID not found, bail early.
			return;
		}

		// Get the modal element.
		this.modal = document.getElementById( this.modalId ) as TPModalElement|null;

		// Check if modal is found.
		if ( ! this.modal ) {
			// Modal not found, bail.
			return;
		}

		// Events.
		this.addEventListener( 'click', this.openModal.bind( this ) );
		this.modal.addEventListener( 'close', this.handleModalClose.bind( this ) );

		// Initialize remaining properties.
		this.modalContentElement = this.modal.querySelector<HTMLElement>( '.modal__content' );
		this.modalBodyElement = this.modalContentElement?.querySelector( '.modal__body' );

		// Check if modalContent found
		if ( this.modalContentElement && this.modalBodyElement ) {
			/**
			 * We are observing the modal instead of the modalContentElement
			 * because the max height of modalContentElement depends on the
			 * height of the viewport and modal is supposed to take up the whole
			 * viewport.
			 */
			this.modalResizeObserver = new ResizeObserver( this.observeModalHeight.bind( this ) );
			this.modalResizeObserver.observe( this.modal );
		}
	}

	/**
	 * Event: 'click'.
	 *
	 * Opens Modal On Element Click.
	 */
	openModal() {
		// Declare modal.
		const modal = this.modal;

		// Open the modal.
		modal?.open();

		// Removes scroll from body.
		document.querySelector( 'body' )?.classList?.add( 'prevent-scroll' );

		// Add animation.
		modal?.classList.add( 'modal--open' );
		modal?.addEventListener( 'animationend',
			() => modal?.classList.remove( 'modal--open' ),
			{ once: true }
		);
	}

	/**
	 * Event: 'close'.
	 *
	 * Handles the close event of TPModalElement.
	 *
	 * @param { Event } evt
	 */
	handleModalClose( evt: Event ) {
		// Assign for easy use in the event handler.
		const modal = this.modal;

		// Check event target.
		if ( modal !== evt.target ) {
			// Modal is not the target, bail.
			return;
		}

		// Check if modal is there.
		if ( ! modal ) {
			//modal not found, bail.
			return;
		}

		// Reopen the modal.
		modal.setAttribute( 'open', 'yes' );

		// Remove and add classes for slide out animation.
		modal.classList.add( 'modal--close' );
		modal.addEventListener( 'animationend', function() {
			// Slide out.
			modal.classList.remove( 'modal--close' );
			modal.removeAttribute( 'open' );
			document.querySelector( 'body' )?.classList?.remove( 'prevent-scroll' );
		}, { once: true } );
	}

	/**
	 * Observes and resizes the body according to the size of its sibling
	 * elements and the containing modal__content.
	 *
	 * @param { ResizeObserverEntry[] } entries
	 */
	observeModalHeight( entries: ResizeObserverEntry[]|null[] ): void {
		// Loop over entries.
		entries.forEach( this.resizeModalBody.bind( this ) );
	}

	/**
	 * Resizes the modal body dynamically.
	 */
	resizeModalBody(): void {
		// Check if we have content and element.
		if ( ! this.modalContentElement || ! this.modalBodyElement ) {
			// We need both for this.
			return;
		}

		// Create references.
		const modalContentElement = this.modalContentElement as HTMLElement;
		const modalBodyElement = this.modalBodyElement as HTMLElement;

		// Get modal content max height.
		let modalContentMaxHeight = parseInt( getComputedStyle( modalContentElement ).maxHeight );
		modalContentMaxHeight = ! Number.isNaN( modalContentMaxHeight ) ? modalContentMaxHeight : 0;

		// Calculate notBodyHeight
		const modalContentRect = modalContentElement.getBoundingClientRect();
		const modalBodyRect = modalBodyElement.getBoundingClientRect();
		let notBodyHeight = modalContentElement.scrollHeight - modalBodyRect.height;

		/**
		 * When we increase the viewport height, the maxHeight and current Height
		 * always have a difference of 1px when that resize happens. We want to
		 * re-introduce that when the viewport height is decreased to avoid discrepancies.
		 */
		if ( modalContentMaxHeight === modalContentRect.height ) {
			notBodyHeight++;
		}

		// New max height candidate for modal__body
		const modalBodyMaxHeightCandidate = modalContentMaxHeight - notBodyHeight;

		// Set the modal body element height.
		modalBodyElement.style.maxHeight = `${ Math.min( modalBodyMaxHeightCandidate, modalBodyElement.scrollHeight ) }px`;
	}
}
