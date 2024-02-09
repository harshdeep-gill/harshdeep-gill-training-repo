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
	private modalOpenButtonElement: HTMLButtonElement | null | undefined;

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
		this.modalOpenButtonElement = this.querySelector<HTMLButtonElement>( 'button' );
		this.modalOpenButtonElement?.addEventListener( 'click', this.openModal.bind( this ) );
		this.modal.addEventListener( 'close', this.handleModalClose.bind( this ) );

		// Resize observer.
		this.modalResizeObserver = new ResizeObserver( this.observeModalHeight.bind( this ) );
		this.modalContentElement = this.modal.querySelector<HTMLElement>( '.modal__content' );

		// Check if modalContent found
		if ( this.modalContentElement ) {
			/**
			 * We are observing the modal instead of the modalContentElement
			 * because the max height of modalContentElement depends on the
			 * height of the viewport and modal is supposed to take up the whole
			 * viewport.
			 */
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
	 */
	handleModalClose() {
		// Assign for easy use in the event handler.
		const modal = this.modal;

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
		// Assign the target.
		const modalContentElement = this.modalContentElement;

		// Bail early if null.
		if ( ! modalContentElement ) {
			// Bail early.
			return;
		}

		// Get modal body.
		const modalBodyElement = modalContentElement.querySelector<HTMLElement>( '.modal__body' );

		// Unobserve the element if modalBodyElement is not present.
		if ( ! modalBodyElement || ! this.modal ) {
			// Check done to stop TS from complaining.
			if ( this.modal ) {
				this.modalResizeObserver?.unobserve( this.modal );
			}

			// Bail early.
			return;
		}

		// Get the parent of modalBodyElement.
		const modalBodyParent = modalBodyElement.parentElement;
		const modalBodySiblingGap = parseInt( modalBodyParent ? getComputedStyle( modalBodyParent ).gap : '0' );

		// Get modal content height.
		let modalContentMaxHeight = parseInt( getComputedStyle( modalContentElement ).maxHeight );
		modalContentMaxHeight = ! Number.isNaN( modalContentMaxHeight ) ? modalContentMaxHeight : 0;
		const {
			paddingTop: modalContentPaddingTop,
			paddingBottom: modalContentPaddingBottom,
		} = getComputedStyle( modalContentElement );

		// Initialize the height to be subtracted.
		const paddingAndGap = parseInt(
			modalContentPaddingTop ? modalContentPaddingTop : '0'
		) + parseInt(
			modalContentPaddingBottom ? modalContentPaddingBottom : '0'
		) + ( ! Number.isNaN( modalBodySiblingGap ) ? modalBodySiblingGap * 2 : 0 );

		// Get header and footer.
		const modalHeaderElement = modalContentElement.querySelector<HTMLElement>( '.modal__header' );
		const modalFooterElement = modalContentElement.querySelector<HTMLElement>( '.modal__footer' );
		let modalHeaderHeight = 0;
		let modalFooterHeight = 0;

		// Get header height.
		if ( modalHeaderElement ) {
			modalHeaderHeight = modalHeaderElement.getBoundingClientRect().height;
		}

		// Get footer height.
		if ( modalFooterElement ) {
			modalFooterHeight = modalFooterElement.getBoundingClientRect().height;
		}

		// New max height candidate for modal__body
		const modalBodyMaxHeightCandidate = (
			modalContentMaxHeight - paddingAndGap - modalFooterHeight - modalHeaderHeight
		);

		// Set the modal body element height.
		modalBodyElement.style.maxHeight = `${ Math.min( modalBodyMaxHeightCandidate, modalBodyElement.scrollHeight ) }px`;
	}
}
