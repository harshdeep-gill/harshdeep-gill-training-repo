/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Tooltip Class.
 */
export class Tooltip extends HTMLElement {
	/**
	 * Properties.
	 */
	private tooltipContent: HTMLElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.tooltipContent = this.querySelector( '.tooltip__description' );

		// Events.
		this.addEventListener( 'mouseenter', this.positionTooltip.bind( this ) );
		this.addEventListener( 'mouseleave', this.handleMouseLeave.bind( this ) );
	}

	/**
	 * Postion of tooltip.
	 */
	positionTooltip() {
		// Check if tooltip is available.
		if ( ! this.tooltipContent ) {
			// Retrun if the tooltip content is not available.
			return;
		}

		// Get the rect of the tooltip.
		const tooltipRect = this.getBoundingClientRect();
		const tooltipContent = this.tooltipContent.getBoundingClientRect();
		const tooltipContentHeight = tooltipContent.height;
		const tooltipContentWidth = tooltipContent.width;

		// Top position of tooltip text.
		const tooltipTextTop = tooltipRect.bottom - tooltipContentHeight - 26;

		// Left position of tooltip text.
		let tooltipTextLeft = tooltipRect.left + ( tooltipRect.width / 2 ) - ( tooltipContentWidth / 2 );

		// If tooltip text is going outsite of screen in left side them give 10px from left.
		if ( tooltipTextLeft <= 0 ) {
			tooltipTextLeft = 10;
		}

		// Set tooltip position.
		this.tooltipContent.style.top = tooltipTextTop + 'px';
		this.tooltipContent.style.left = tooltipTextLeft + 'px';

		// If tooltip text is going outsite of screen in right side them give 10px from right and reset left position.
		if ( tooltipTextLeft + tooltipContentWidth >= window.innerWidth ) {
			this.tooltipContent.style.left = '';
			this.tooltipContent.style.right = '10px';
		}
	}

	/**
	 * Event: Mouse Leave.
	 */
	handleMouseLeave() {
		// Check if the tooltip content is available.
		if ( this.tooltipContent ) {
			this.tooltipContent.style.top = '';
			this.tooltipContent.style.left = '';
			this.tooltipContent.style.right = '';
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-tooltip', Tooltip );
