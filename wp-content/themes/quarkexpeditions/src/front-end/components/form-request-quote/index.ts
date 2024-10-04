/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * External dependency
 */
import { TPFormFieldElement, TPTabsElement } from '@travelopia/web-components';

/**
 * FormRequestQuote Class.
 */
export default class FormRequestQuote extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly tabs: TPTabsElement | null;
	private nextStepButton: HTMLButtonElement | null;
	private previousStepButton: HTMLButtonElement | null;
	private stepOneFormFields: NodeListOf<TPFormFieldElement> | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.tabs = this.querySelector( '.form-request-quote__tabs' );
		this.nextStepButton = this.querySelector( '.form-request-quote__next-step-btn' );
		this.previousStepButton = this.querySelector( '.form-request-quote__previous-step-button' );
		this.stepOneFormFields = this.querySelectorAll( '.form-request-quote__step-1 .form-field' );

		// Events
		this.nextStepButton?.addEventListener( 'click', () => this.handleStepOneValidation() );
		this.previousStepButton?.addEventListener( 'click', () => this.goToPreviousStep() );
	}

	/**
	 * Handle step one validation.
	 */
	handleStepOneValidation() {
		// Set the validation constant.
		const isStepOneValidated = this.isStepOneValid();

		// Check if step one is validated.
		if ( isStepOneValidated ) {
			// Set the current tab.
			this.tabs?.setCurrentTab( 'contact-details' );
		}
	}

	/**
	 * Validate the step one.
	 */
	isStepOneValid() {
		// Let's assume all form fields are valid
		let isStepOneValidated = true;

		// For each form field.
		this.stepOneFormFields?.forEach( ( formField ) => {
			// Set the is valid state.
			const isValid = formField.validate();

			// If even one of the form field is invalid, set the isStepOneValidated to false.
			if ( ! isValid ) {
				// Set the isStepOneValidated to false.
				isStepOneValidated = false;
			}
		} );

		// Return the isStepOneValidated.
		return isStepOneValidated;
	}

	/**
	 * Go to previous step.
	 */
	goToPreviousStep() {
		// Go to the previous step.
		this.tabs?.setCurrentTab( 'travel-details' );
	}
}

// Define element.
customElements.define( 'quark-form-request-quote', FormRequestQuote );
