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
		const triggerRect = this.getBoundingClientRect();
		const tooltipRect = this.tooltipContent.getBoundingClientRect();

		// Above the trigger.
		let top = triggerRect.top - tooltipRect.height - 10;
		let left = triggerRect.left + ( triggerRect.width / 2 ) - ( tooltipRect.width / 2 );

		// If there's no space above, place it below.
		if ( top < window.scrollY ) {
			top = triggerRect.bottom + 5;
		}

		// If the tooltip goes off the left edge of the screen, align it to the left edge of the trigger.
		if ( left < 0 ) {
			left = triggerRect.left;
		} else if ( left + tooltipRect.width > window.innerWidth ) { // If it goes off the right edge, align it to the right edge of the trigger
			left = triggerRect.right - tooltipRect.width;
		}

		// Set top and left positions.
		this.tooltipContent.style.top = `${ top }px`;
		this.tooltipContent.style.left = `${ left }px`;
	}

	/**
	 * Event: Mouse Leave.
	 */
	handleMouseLeave() {
		// Check if the tooltip content is available.
		if ( this.tooltipContent ) {
			this.tooltipContent.style.top = '';
			this.tooltipContent.style.left = '';
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-tooltip', Tooltip );
