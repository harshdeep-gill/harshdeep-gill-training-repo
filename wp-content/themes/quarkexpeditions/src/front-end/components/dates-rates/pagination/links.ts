/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies
 */
import DatesRatesPaginationNextPageElement from './next';
import DatesRatesPaginationPrevPageElement from './prev';

/**
 * Store
 */
const { subscribe, getState } = zustand.stores.datesRates;

/**
 * Links controller class.
 */
export default class DatesRatesPaginationLinksControllerElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly pageNumberTemplate: HTMLTemplateElement | null;
	private readonly dotsTemplate: HTMLTemplateElement | null;
	private readonly prevPageElement: DatesRatesPaginationPrevPageElement | null;
	private readonly nextPageElement: DatesRatesPaginationNextPageElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.pageNumberTemplate = this.querySelector( '.dates-rates__template-pagination-page' );
		this.dotsTemplate = this.querySelector( '.dates-rates__template-pagination-dots' );
		this.prevPageElement = this.querySelector( 'quark-dates-rates-pagination-prev-page' );
		this.nextPageElement = this.querySelector( 'quark-dates-rates-pagination-next-page' );

		// Subscribe to the store.
		subscribe( this.update.bind( this ) );

		// Initial sync.
		this.update( getState() );
	}

	/**
	 * Update the component.
	 *
	 * @param {Object} state The state object.
	 */
	update( state: DatesRatesState ) {
		// Null checks.
		if ( ! (
			this.pageNumberTemplate &&
			this.dotsTemplate &&
			this.prevPageElement &&
			this.nextPageElement
		) ) {
			// Bail.
			return;
		}

		// Get the state.
		const { pageNumber, totalPages } = state;

		// Initialize page number start.
		let pageNumberBegin = pageNumber;
		let pageNumberEnd = totalPages;
		const pagesLeft = totalPages - pageNumber;
		const maxNumlinks = 5;

		// Check how many pages are left and update accordingly.
		if ( maxNumlinks > pagesLeft ) {
			pageNumberEnd = totalPages;

			// If there are not enough pages.
			if ( totalPages <= maxNumlinks ) {
				pageNumberBegin = 1;
			} else {
				pageNumberBegin = totalPages - maxNumlinks + 1;
			}
		} else {
			pageNumberEnd = pageNumberBegin + maxNumlinks - 1;
		}

		// Remove the existing elements
		let currentNode = this.prevPageElement.nextElementSibling;

		// Check if we have a valid node
		if ( currentNode && currentNode !== this.nextPageElement ) {
			// Loop through the nodes.
			while ( currentNode && currentNode !== this.nextPageElement ) {
				// Is this a template element?
				if ( 'TEMPLATE' === currentNode.tagName ) {
					// Do nothing.
					currentNode = currentNode.nextElementSibling;
					continue;
				}

				// Delete.
				const next = currentNode.nextElementSibling;
				this.removeChild( currentNode );

				// Advance the pointer.
				currentNode = next;
			}
		}

		// Last insert.
		let lastInsertedElement: Element | null = this.prevPageElement;

		// Loop through the range.
		for ( let i = pageNumberBegin; i <= pageNumberEnd; ++i ) {
			// Get the template clone.
			const pageNumberElement = ( this.pageNumberTemplate.content.cloneNode( true ) as HTMLElement ).querySelector( 'quark-dates-rates-pagination-page-number' );

			// Null check.
			if ( ! pageNumberElement ) {
				// Bail.
				return;
			}

			// Set the `number` attribute
			pageNumberElement.setAttribute( 'number', i.toString() );

			// Set the text.
			const button = pageNumberElement.querySelector( 'button' );

			// Get the button and set text.
			if ( button ) {
				button.textContent = i.toString();
			}

			// Add to the insert list.
			lastInsertedElement.insertAdjacentElement( 'afterend', pageNumberElement );
			lastInsertedElement = pageNumberElement;
		}

		// Get the dots ready.
		const dots = ( this.dotsTemplate.content.cloneNode( true ) as HTMLElement ).querySelector( '.page-numbers.dots' );

		// Toggle the `first` link.
		if ( 1 !== pageNumber ) {
			this.prevPageElement.previousElementSibling?.removeAttribute( 'data-hidden' );
		} else {
			this.prevPageElement.previousElementSibling?.setAttribute( 'data-hidden', '' );
		}

		// Toggle the last page link.
		if ( totalPages !== pageNumber ) {
			this.nextPageElement.nextElementSibling?.removeAttribute( 'data-hidden' );
		} else {
			this.nextPageElement.nextElementSibling?.setAttribute( 'data-hidden', '' );
		}

		// Check and insert dots.
		if ( dots && totalPages > maxNumlinks ) {
			// Check if to insert at beginning.
			if ( 1 !== pageNumber ) {
				this.prevPageElement.insertAdjacentHTML( 'afterend', dots.outerHTML );
			}

			// Check if to insert at end.
			if ( pageNumberEnd !== totalPages ) {
				this.nextPageElement.insertAdjacentHTML( 'beforebegin', dots.outerHTML );
			}
		}
	}
}
