/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Internal dependencies.
 */
import { SearchFilterDestinations } from './index';

/**
 * Class SearchFilterDestinationsOption.
 */
export class SearchFilterDestinationOption extends HTMLElement {
	/**
	 * Properties.
	 */
	private searchFiltersModal: HTMLElement | null;
	private destinationSelector: SearchFilterDestinations | null | undefined;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize super.
		super();

		// Elements.
		this.searchFiltersModal = document.querySelector( '.search-filters-bar__modal' );
		this.destinationSelector = this.searchFiltersModal?.querySelector( 'quark-search-filters-bar-destinations' );

		// Event Listeners.
		this.addEventListener( 'click', this.handleOptionSelection.bind( this ), { capture: true } );
	}

	/**
	 * Handle selection of destination option.
	 *
	 * @param {Event} e Event.
	 */
	handleOptionSelection( e: Event | null ) {
		// Prevent default behavior and stop propagation.
		e?.preventDefault();
		e?.stopPropagation();

		// Get current target.
		const currentOption = e?.target as HTMLElement;

		// Get the parent element of the selected option.
		const optionParentElement = currentOption.closest( 'quark-search-filters-bar-destinations-option' );

		// Set selected attribute for current option.
		if ( optionParentElement ) {
			const value: string = optionParentElement.getAttribute( 'value' ) ?? '';

			// Toggle selected state. Dispatch custom events accordingly.
			if (
				'yes' !== optionParentElement.getAttribute( 'selected' )
			) {
				this.destinationSelector?.select( value );
				this.destinationSelector?.dispatchEvent( new CustomEvent( 'select', {
					detail: { value },
				} ) );
			} else {
				this.destinationSelector?.unSelect( value );
				this.destinationSelector?.dispatchEvent( new CustomEvent( 'unselect', {
					detail: { value },
				} ) );
			}
		}
	}
}

// Define Element.
customElements.define( 'quark-search-filters-bar-destinations-option', SearchFilterDestinationOption );
