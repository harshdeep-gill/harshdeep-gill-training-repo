/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * FormTwoStepModalCTA Class.
 */
export default class FormTwoStepModalCTA extends HTMLElement {
	/**
	 * Properties.
	 */
	private modalOpenBtn: HTMLElement | null;
	private readonly modalId: String | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.modalOpenBtn = this.querySelector( 'quark-modal-open' );
		this.modalId = this.dataset.modalId ?? '';

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
			ship: this.dataset.ship ?? '',
			expedition: this.dataset.expedition ?? '',
		};

		// Hidden Fields.
		const fields: Fields = {
			polarRegion: form.querySelector( '.form__polar-region-field' ),
			ship: form.querySelector( '.form__ship-field' ),
			expedition: form.querySelector( '.form__expedition-field' ),
		};

		// Populate the values into the form fields.
		for ( const [ key, formFieldEl ] of Object.entries( fields ) ) {
			// Skip, if form field is not present.
			if ( ! formFieldEl ) {
				continue;
			}

			// Set the attribute for the form field.
			formFieldEl.setAttribute( 'value', hiddenFieldValues[ key ] );
		}
	}
}
