/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * DatesRatesTable Class.
 * This class handles the adjustment of the rowspan attribute
 * for a table with the class 'dates-rates__table'.
 */
export default class DatesRatesTable extends HTMLElement {
	/**
	 * Properties.
	 * 'table' will hold the reference to the table element, if it exists.
	 */
	private table: HTMLTableElement | null = null;

	/**
	 * Constructor.
	 * Initializes the component and sets up the necessary event listeners.
	 */
	constructor() {
		// Call the parent constructor
		super();

		// Get the table element with the class 'dates-rates__table' within this component.
		this.table = this.querySelector<HTMLTableElement>( '.dates-rates__table' );

		// If the table exists, add the event listener to adjust rowspan when the DOM is fully loaded.
		if ( this.table ) {
			document.addEventListener( 'DOMContentLoaded', this.adjustRowspan.bind( this ) );
		}
	}

	/**
	 * Adjusts the rowspan of the first column in the table to span all rows.
	 */
	private adjustRowspan(): void {
		// Ensure the table exists.
		if ( ! this.table ) {
			// No, bail.
			return;
		}

		// Get all rows in the table.
		const rows = this.table.getElementsByTagName( 'tr' );

		// If there are no rows, return early.
		if ( rows.length === 0 ) {
			// No, bail.
			return;
		}

		// Assume the first column should span all rows, find the first cell in the first row.
		const firstColumn = rows[ 1 ].cells[ 0 ];

		// If the first column cell is not found, return early
		if ( ! firstColumn ) {
			// No, bail.
			return;
		}

		// Set the rowspan of the first column to span all rows except the header row.
		const rowSpanCount = rows.length - 1; // Subtract the header row
		firstColumn.rowSpan = rowSpanCount;
		firstColumn.classList.add( 'dates-rates__item-table-column--first' );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-dates-rates-table', DatesRatesTable );
