/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * QuarkRangeSlider Class.
 */
export default class QuarkRangeSlider extends HTMLElement {
	/**
	 * Properties.
	 */
	private rangeSlider: HTMLInputElement | null;
	private rangeInputElements: NodeListOf<HTMLInputElement> | null;
	private rangeDisplay: HTMLSpanElement | null;
	private minInputElement: HTMLInputElement | null;
	private maxInputElement: HTMLInputElement;
	private defaultMinValue: string;
	private defaultMaxValue: string;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.rangeSlider = this.querySelector( '.form__range-slider-track' );
		this.rangeInputElements = this.querySelectorAll( '.form__range-slider__input' );
		this.rangeDisplay = this.querySelector( '.form__range-slider-range' );
		this.minInputElement = this.rangeInputElements[ 0 ];
		this.maxInputElement = this.rangeInputElements[ 1 ];
		this.defaultMinValue = this.getAttribute( 'min' ) ?? '0';
		this.defaultMaxValue = this.getAttribute( 'max' ) ?? '0';

		// Add event listeners.
		this.rangeSlider?.addEventListener( 'input', this.setArea.bind( this ) );
		this.minInputElement.addEventListener( 'input', this.handleMinInput.bind( this ) );
		this.maxInputElement.addEventListener( 'input', this.handleMaxInput.bind( this ) );

		// Invoke.
		this.setArea();
		this.updateRangeDisplay();
	}

	/**
	 * Set the range of the slider.
	 *
	 * @param {number} min
	 * @param {number} max
	 *
	 * @return {void}
	 */
	setRange( min: number = 0, max: number = 0 ): void {
		// Check if min and max values are set.
		if ( null === min || null === max ) {
			// Return.
			return;
		}

		// Check if min and max values are set.
		if ( ! this.minInputElement || ! this.maxInputElement ) {
			// Return.
			return;
		}

		// Set min and max attribute to element.
		this.setAttribute( 'min', min.toString() );
		this.setAttribute( 'max', max.toString() );

		// Set min and max attribute to Min Input Element.
		this.minInputElement.setAttribute( 'min', min.toString() );
		this.minInputElement.setAttribute( 'max', max.toString() );

		// Set min and max attribute to Max Input Element.
		this.maxInputElement.setAttribute( 'min', min.toString() );
		this.maxInputElement.setAttribute( 'max', max.toString() );

		// Invoke.
		this.setArea();
		this.updateRangeDisplay();
	}

	/**
	 * Set the prefix of the range slider.
	 *
	 * @param {string} prefix
	 *
	 * @return {void}
	 */
	setPrefix( prefix: string = '' ): void {
		// Check if prefix is set.
		if ( null === prefix ) {
			// Return.
			return;
		}

		// Set prefix attribute to element.
		this.setAttribute( 'prefix', prefix );

		// Invoke.
		this.updateRangeDisplay( false );
	}

	/**
	 * Set the suffix of the range slider.
	 *
	 * @param {string} suffix
	 *
	 * @return {void}
	 */
	setSuffix( suffix: string = '' ): void {
		// Check if suffix is set.
		if ( null === suffix ) {
			// Return.
			return;
		}

		// Set suffix attribute to element.
		this.setAttribute( 'suffix', suffix );

		// Invoke.
		this.updateRangeDisplay( false );
	}

	/**
	 * Handle min value input.
	 */
	handleMinInput() {
		// Check if min and max values are set.
		if ( ! this.minInputElement || ! this.maxInputElement ) {
			// Return.
			return;
		}

		// Get min and max values.
		const minValue: number = parseInt( this.minInputElement.value );
		const maxValue: number = parseInt( this.maxInputElement.value );

		// Prevent min value from exceeding max value.
		if ( minValue >= maxValue ) {
			this.minInputElement.value = ( maxValue ).toString();
		}

		// Add z-index to the input, for slide priority.
		this.minInputElement.style.zIndex = '1';
		this.maxInputElement.style.zIndex = '0';

		// Set area of range and update range display.
		this.setArea();
		this.updateRangeDisplay();

		// Dispatch custom event.
		this.dispatchEvent( new CustomEvent( 'change', {
			detail: {
				selectedValues: [ minValue, maxValue ],
			},
		} ) );
	}

	/**
	 * Handle max value input.
	 */
	handleMaxInput() {
		// Check if min and max values are set.
		if ( ! this.minInputElement || ! this.maxInputElement ) {
			// Return.
			return;
		}

		// Get min and max values.
		const minValue: number = parseInt( this.minInputElement.value );
		const maxValue: number = parseInt( this.maxInputElement.value );

		// Prevent max value from being less than min value.
		if ( maxValue <= minValue ) {
			this.maxInputElement.value = ( minValue ).toString();
		}

		// Add z-index to the input, for slide priority.
		this.maxInputElement.style.zIndex = '1';
		this.minInputElement.style.zIndex = '0';

		// Set area of range and update range display.
		this.setArea();
		this.updateRangeDisplay();

		// Dispatch custom event.
		this.dispatchEvent( new CustomEvent( 'change', {
			detail: {
				selectedValues: [ minValue, maxValue ],
			},
		} ) );
	}

	/**
	 * Set the area of the range slider.
	 */
	setArea() {
		// Check if range slider, min value and max value are set.
		if ( ! this.rangeSlider || ! this.minInputElement || ! this.maxInputElement ) {
			// Return.
			return;
		}

		// Get the min and max values of the overall slider.
		const sliderMinValue: number = parseInt( this.getAttribute( 'min' ) ?? '0' );
		const sliderMaxValue: number = parseInt( this.getAttribute( 'max' ) ?? '0' );

		// Check if the values are set.
		if ( ! sliderMinValue || ! sliderMaxValue ) {
			// Return.
			return;
		}

		// Get min and max values of the input.
		const minValue: number = parseInt( this.minInputElement.value );
		const maxValue: number = parseInt( this.maxInputElement.value );

		// If the gap between min value and max value is less than 0, return.
		if ( ( maxValue - minValue ) < 0 ) {
			// Return.
			return;
		}

		// Set the range slider and color the range.
		this.rangeSlider.style.left = `${ ( ( minValue - sliderMinValue ) / ( sliderMaxValue - sliderMinValue ) ) * 100 }%`;
		this.rangeSlider.style.right = `${ 100 - ( ( ( maxValue - sliderMinValue ) / ( sliderMaxValue - sliderMinValue ) ) * 100 ) }%`;
	}

	/**
	 * Update the range display.
	 *
	 * @param {boolean} updateRangeDisplay Whether to update 'selected-value' attribute.
	 */
	updateRangeDisplay( updateRangeDisplay = true ) {
		// Check if range display, min value and max value are set.
		if ( ! this.rangeDisplay || ! this.minInputElement || ! this.maxInputElement ) {
			// Return.
			return;
		}

		// Get min and max values.
		const minValue: number = parseInt( this.minInputElement.value );
		const maxValue: number = parseInt( this.maxInputElement.value );

		// Get the range prefix, suffix if set.
		const rangePrefix: string = this.getAttribute( 'prefix' ) ?? '';
		const rangeSuffix: string = this.getAttribute( 'suffix' ) ?? '';

		// Check if 'updateRangeDisplay' is true.
		if ( updateRangeDisplay ) {
			// Set selected value.
			this.setAttribute( 'selected-value', JSON.stringify( [ minValue, maxValue ] ) );
		}

		// Update the range display, along with the prefix and suffix.
		this.rangeDisplay.textContent = ` ${ rangePrefix }${ minValue } - ${ rangePrefix }${ maxValue }  ${ rangeSuffix }`;
	}

	/**
	 * Setter
	 *
	 * @param {Array} values Values [ minValue, maxValue ]
	 */
	setValues( values: number[] = [] ) {
		// Set selected value.
		this.setAttribute( 'selected-value', JSON.stringify( values ) );

		// Check if minInputElement exists.
		if ( this.minInputElement ) {
			// Set the value of minInputElement.
			this.minInputElement.value = values[ 0 ] ? values[ 0 ].toString() : this.defaultMinValue;
		}

		// Check if maxInputElement exists.
		if ( this.maxInputElement ) {
			// Set the value of maxInputElement.
			this.maxInputElement.value = values[ 1 ] ? values[ 1 ].toString() : this.defaultMaxValue;
		}

		// Set area and range.
		this.setArea();
		this.updateRangeDisplay();
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-range-slider', QuarkRangeSlider );
