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
	private readonly quarkForm: HTMLElement | null;
	private readonly successMessage: HTMLElement | null;
	private readonly content: HTMLElement | null;
	private readonly tabs: TPTabsElement | null;
	private nextStepButton: HTMLButtonElement | null;
	private previousStepButton: HTMLButtonElement | null;
	private stepOneFormFields: NodeListOf<TPFormFieldElement> | null;
	private expeditions: HTMLSelectElement | null;
	private monthOptionsContainer: HTMLDivElement;
	private monthOptionTemplate: HTMLTemplateElement | null;
	private filtersEndpoint: string;
	private readonly toastMessage: ToastMessage | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.quarkForm = this.querySelector( 'quark-form' );
		this.successMessage = this.querySelector( '.form-request-quote__success' );
		this.content = this.querySelector( '.form-request-quote__tabs' );
		this.tabs = this.querySelector( '.form-request-quote__tabs' );
		this.nextStepButton = this.querySelector( '.form-request-quote__next-step-btn' );
		this.previousStepButton = this.querySelector( '.form-request-quote__previous-step-button' );
		this.stepOneFormFields = this.querySelectorAll( '.form-request-quote__step-1 .form-field' );
		this.filtersEndpoint = this.dataset.filtersEndpoint || '';
		this.expeditions = this.querySelector( '.form-request-quote__expedition' );
		this.monthOptionsContainer = this.querySelector( '.form-request-quote__options .form-field-group__group' ) as HTMLDivElement;
		this.monthOptionTemplate = this.querySelector( '.form-request-quote__template-month-option' );
		this.toastMessage = this.querySelector( 'quark-toast-message' );

		// Events
		this.nextStepButton?.addEventListener( 'click', () => this.handleStepOneValidation() );
		this.previousStepButton?.addEventListener( 'click', () => this.goToPreviousStep() );
		this.expeditions?.addEventListener( 'change', () => this.changeExpedition() );
		this.quarkForm?.addEventListener( 'validation-error', this.showToastMessage.bind( this ) );
		this.quarkForm?.addEventListener( 'validation-success', this.hideToastMessage.bind( this ) );
		this.quarkForm?.addEventListener( 'api-success', this.showSuccessMessage.bind( this ) );

		// Add radio button toggle event listener.
		this.initializeRadioToggle();
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

			// Hide the toast message.
			this.hideToastMessage();
		} else {
			// Show the toast message.
			this.showToastMessage();
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

	/**
	 * Show toast message.
	 */
	showToastMessage(): void {
		// Show toast message.
		this.toastMessage?.show();
	}

	/**
	 * Hide toast message.
	 */
	hideToastMessage(): void {
		// Hide toast message.
		this.toastMessage?.hide();
	}

	/**
	 * Initialize radio button toggle functionality.
	 */
	initializeRadioToggle() {
		// Get all the radio buttons.
		const radios = this.querySelectorAll( '.form-request-quote__contact-method input[type="radio"]' );

		// Track the last checked radio button.
		let lastCheckedRadio: HTMLInputElement | null = null;

		// For each loop.
		radios.forEach( ( radio ) => {
			// Set the radio element.
			const radioElement = radio as HTMLInputElement;

			// Click event.
			radioElement.addEventListener( 'click', ( event ) => {
				// Set the target element.
				const target = event.target as HTMLInputElement;

				// Check if the current radio was already checked
				if ( target === lastCheckedRadio ) {
					// If it was already checked, uncheck it
					target.checked = false;

					// Reset the tracking variable.
					lastCheckedRadio = null;
				} else {
					// If it's a new selection, uncheck all others and set this one as checked
					radios.forEach( ( otherRadio ) => {
						// Set the other radio elements.
						const otherRadioElement = otherRadio as HTMLInputElement;

						// Uncheck other radios.
						otherRadioElement.checked = false;
					} );

					// Manually set the clicked radio to checked.
					target.checked = true;

					// Store the reference to the checked radio.
					lastCheckedRadio = target;
				}
			} );
		} );
	}

	/**
	 * Show thank you message.
	 */
	showSuccessMessage(): void {
		// Check if we have content and thank you.
		if ( ! this.content || ! this.successMessage ) {
			// We don't, bail!
			return;
		}

		// Hide content and show thank you instead.
		this.content.classList.add( 'form-request-quote__tabs-hidden' );
		this.successMessage.classList.add( 'form-request-quote__success-visible' );

		// Scroll to the success message
		this.scrollToSuccessMessage();
	}

	/**
	 * Scroll to the success message element.
	 */
	scrollToSuccessMessage(): void {
		// Set the scroll element.
		const successElement = document.getElementById( 'form-request-quote__success' );

		// Check if the element exists.
		if ( successElement ) {
			// Scroll to the element.
			window.scrollTo( {
				top: successElement.offsetTop,
				behavior: 'smooth',
			} );
		}
	}

	/**
	 * Change expedition.
	 */
	async changeExpedition() {
		// Clear the container.
		this.monthOptionsContainer.innerHTML = '';

		// Get Selected expedition.
		const selectedExpeditionId: string = this.expeditions?.querySelector( 'tp-multi-select-option[selected="yes"]' )?.getAttribute( 'value' ) as string;

		// Month options to be displayed.
		const monthOptions = await this.fetchMonthOptions( selectedExpeditionId );

		// Add default option.
		const defaultOption = ( this.monthOptionTemplate?.content.cloneNode( true ) as HTMLDivElement ).querySelector( '.checkbox-container' ) as HTMLDivElement;
		const defaultInput = defaultOption?.querySelector( 'input' ) as HTMLInputElement;
		const defaultLabel = defaultOption?.querySelector( 'label' ) as HTMLLabelElement;

		// Set the default option attributes.
		defaultInput.setAttribute( 'value', 'any_available_departure' );
		defaultLabel.innerHTML = 'Any Available Departure';

		// Append the default option to the month options container.
		this.monthOptionsContainer?.appendChild( defaultOption );

		// Loop through the month options.
		for ( const month of monthOptions ) {
			// Create the option.
			const option = ( this.monthOptionTemplate?.content.cloneNode( true ) as HTMLDivElement ).querySelector( '.checkbox-container' ) as HTMLDivElement;
			const input = option?.querySelector( 'input' ) as HTMLInputElement;
			const label = option?.querySelector( 'label' ) as HTMLLabelElement;

			// Null check.
			if ( ! option ) {
				// Skip the iteration.
				return;
			}

			// Format the month value.
			const monthValue = month.value.split( '-' );
			const formattedValue = `${ monthValue[ 0 ] }-${ monthValue[ 1 ] }`;

			// Set the option attributes.
			input.setAttribute( 'value', formattedValue );
			input.setAttribute( 'id', `month-${ month.value }` );
			label.setAttribute( 'for', `month-${ month.value }` );
			label.innerHTML = month.label;

			// Append the option to the month options container.
			this.monthOptionsContainer.appendChild( option );
		}
	}

	/**
	 * Fetch month options.
	 *
	 * @param { string } expeditionId Expedition ID.
	 */
	async fetchMonthOptions( expeditionId: string ): Promise<Array<any>> {
		// Fetch month options.
		const response = await fetch( `${ this.filtersEndpoint }?expedition_id=${ expeditionId }` );
		const data = await response.json();

		// Set the month options.
		return data.months;
	}
}

// Define element.
customElements.define( 'quark-form-request-quote', FormRequestQuote );
