
/**
 * Global variables.
*/
const { customElements, HTMLElement } = window;

/**
 * Internal dependencies.
 */
import InquiryForm from "../inquiry-form";

/**
 * LPFormModalCTA Class.
 */
export default class LPFormModalCTA extends HTMLElement {
	/**
	 * Properties.
	 */
	private modalOpenBtn: HTMLElement | null;
	private readonly modalId: String | null;
	private inquiryForm : InquiryForm | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.modalOpenBtn = this.querySelector( 'quark-modal-open' );
		this.modalId = this.dataset.modalId ?? '';
		this.inquiryForm = this.closest( 'quark-inquiry-form' );

		// Events
		this.modalOpenBtn?.addEventListener( 'click', this.handleModalOpenBtnClick.bind( this ) );
	}

	/**
	 * Event: 'click'.
	 *
	 * Handles Modal Open Button Click.
	 */
	handleModalOpenBtnClick() {
		// Get the form inside the modal.
		const form = document.querySelector( `tp-modal#${ this.modalId } form` );

		// Bail, if no form is found.
		if ( ! form ) {
			// Bail.
			return;
		}

		// Hidden Field Values.
		const hiddenFieldValues: HiddenFieldValues = {
			polarRegion: this.dataset.polarRegion ?? '',
			season: this.dataset.season ?? '',
			ship: this.dataset.ship ?? '',
			subRegion: this.dataset.subRegion ?? '',
			expedition: this.dataset.expedition ?? '',
		};

		// Hidden Fields.
		const fields: Fields = {
			polarRegion: form.querySelector( '.form__polar-region-field' ),
			season: form.querySelector( '.form__season-field' ),
			ship: form.querySelector( '.form__ship-field' ),
			subRegion: form.querySelector( '.form__sub-region-field' ),
			expedition: form.querySelector( '.form__expedition-field' ),
		};

		// Populate the values into the form fields.
		for ( const [ key, formFieldEl ] of Object.entries( fields ) ) {
			// Skip, if form field is not present.
			if ( ! formFieldEl ) {
				continue;
			}

			/**
			 * Prevent name collision for input fields.
			 * If a field exists in the outer form with the same name, then disable
			 * the field inside of the modal.
			 */
			if (
				this.inquiryForm &&
				// Check if a field with the same name exists outside.
				this.inquiryForm.querySelector( `[name="${ formFieldEl.getAttribute('name') ?? '' }"]`)
			) {
				formFieldEl.setAttribute( 'disabled', 'disabled' );
			} else {
				formFieldEl.removeAttribute( 'disabled' );
			}

			// Set the attribute for the form field.
			formFieldEl.setAttribute( 'value', hiddenFieldValues[ key ] );
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-lp-form-modal-cta', LPFormModalCTA );
