/**
 * External dependencies.
 */
import { TPMultiSelectElement } from '@travelopia/web-components';

/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * toggleExistingBookingInfoAttribute.
 */
export default class toggleExistingBookingInfoAttribute extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly inquiryType: HTMLElement | null;
	private readonly existingBookingType: TPMultiSelectElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.inquiryType = this.querySelector( '.form-contact-us__inquiry-type' );
		this.existingBookingType = this.querySelector( '.form-contact-us__existing-booking-info tp-multi-select' );

		// Setting values of booking type info back to default when something else then "Existing Bookings" is selected.
		this.inquiryType?.addEventListener( 'toggled-off', () => {
			// Checking if Existing booking type is true and making it empty.
			if ( this.existingBookingType && 'value' in this?.existingBookingType ) {
				this.existingBookingType?.unSelectAll();
			}
		} );
	}
}

// Define element.
customElements.define( 'quark-form-contact-us', toggleExistingBookingInfoAttribute );
