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
	private tooltipContentElement: HTMLElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.tooltipContentElement = this.querySelector( '.tooltip__description' );

		// Events
		this.tooltipContentElement?.addEventListener( 'toggle', this.handleTooltipToggled.bind( this ) );
	}

	/**
	 * Postion of tooltip.
	 */
	positionTooltip() {
		// Check if tooltip is available.
		if ( ! this.tooltipContentElement ) {
			// Retrun if the tooltip content is not available.
			return;
		}

		// Get the rect of the tooltip.
		const tooltipTriggerRect = this.getBoundingClientRect();
		const tooltipTriggerRectOffsets = [
			{ dir: 'top', value: tooltipTriggerRect.top },
			{ dir: 'bottom', value: window.innerHeight - tooltipTriggerRect.bottom },
			{ dir: 'left', value: tooltipTriggerRect.left },
			{ dir: 'right', value: window.innerWidth - tooltipTriggerRect.right },
		].sort( ( offsetA, offsetB ) => offsetB.value - offsetA.value );

		// Get the rect for content element.
		const contentElementRect = this.tooltipContentElement.getBoundingClientRect();

		// Maximum value for height/width.
		const TOOLTIP_CONTENT_MAX_DIMENSION = 360;

		// The distance between content and screen edges / icon and content.
		const MINIMUM_BUFFER_DISTANCE_VALUE = 20;

		// Initialize offsets.
		const largestOffset = tooltipTriggerRectOffsets[ 0 ];
		let secondLargestOffset = tooltipTriggerRectOffsets[ 1 ];
		const tooltipDirection = largestOffset.dir;

		// Initialize max width/height.
		let newMaxWidth = 0;
		let newMaxHeight = 0;

		// Initialize leftValue.
		let leftValue = MINIMUM_BUFFER_DISTANCE_VALUE;

		// Initial top value.
		let topValue = MINIMUM_BUFFER_DISTANCE_VALUE;

		// The largest directional offset.
		if ( [ 'top', 'bottom' ].includes( largestOffset.dir ) ) {
			//
			if ( [ 'top', 'bottom' ].includes( secondLargestOffset.dir ) ) {
				secondLargestOffset = tooltipTriggerRectOffsets[ 2 ];
			}

			// Setup max height.
			if ( largestOffset.value >= TOOLTIP_CONTENT_MAX_DIMENSION + ( 2 * MINIMUM_BUFFER_DISTANCE_VALUE ) ) {
				newMaxHeight = TOOLTIP_CONTENT_MAX_DIMENSION;
			} else {
				newMaxHeight = largestOffset.value - ( 2 * MINIMUM_BUFFER_DISTANCE_VALUE );
			}

			// Setup max width.
			if ( secondLargestOffset.value >= TOOLTIP_CONTENT_MAX_DIMENSION + ( 2 * MINIMUM_BUFFER_DISTANCE_VALUE ) ) {
				newMaxWidth = TOOLTIP_CONTENT_MAX_DIMENSION;
			} else {
				newMaxWidth = secondLargestOffset.value - ( 2 * MINIMUM_BUFFER_DISTANCE_VALUE );
			}

			// If the position is top.
			if ( 'top' === largestOffset.dir ) {
				topValue = tooltipTriggerRect.top - Math.min( contentElementRect.height, newMaxHeight ) - ( 2 * MINIMUM_BUFFER_DISTANCE_VALUE );
			} else {
				topValue = tooltipTriggerRect.bottom + MINIMUM_BUFFER_DISTANCE_VALUE;
			}

			// If the secondary position is left.
			if ( 'left' === secondLargestOffset.dir ) {
				const theRightOffset = tooltipTriggerRectOffsets.find( ( offset ) => 'right' === offset.dir )?.value ?? 0;
				const availableSpaceOnRight = theRightOffset - MINIMUM_BUFFER_DISTANCE_VALUE;

				// Get the mid point of the triggerRect.
				const triggerMidPoint = tooltipTriggerRect.left + ( tooltipTriggerRect.width / 2 );

				// Spread from left to available right.
				if ( availableSpaceOnRight <= Math.min( newMaxWidth, contentElementRect.width ) / 2 ) {
					leftValue = triggerMidPoint - ( Math.min( newMaxWidth, contentElementRect.width ) / 2 ) + availableSpaceOnRight;
				} else {
					leftValue = triggerMidPoint - ( Math.min( newMaxWidth, contentElementRect.width ) / 2 );
				}
			} else {
				const theLeftOffset = tooltipTriggerRectOffsets.find( ( offset ) => 'left' === offset.dir )?.value ?? 0;
				const availableSpaceOnLeft = theLeftOffset - MINIMUM_BUFFER_DISTANCE_VALUE;

				// Get the mid point of the triggerRect.
				const triggerMidPoint = tooltipTriggerRect.left + ( tooltipTriggerRect.width / 2 );

				// Spread from left to available left.
				if ( availableSpaceOnLeft <= Math.min( newMaxWidth, contentElementRect.width ) / 2 ) {
					leftValue = triggerMidPoint - ( Math.min( newMaxWidth, contentElementRect.width ) / 2 ) + availableSpaceOnLeft;
				} else {
					leftValue = triggerMidPoint - ( Math.min( newMaxWidth, contentElementRect.width ) / 2 );
				}
			}
		} else {
			//
			if ( [ 'left', 'right' ].includes( secondLargestOffset.dir ) ) {
				secondLargestOffset = tooltipTriggerRectOffsets[ 2 ];
			}

			// Setup max height.
			if ( largestOffset.value >= TOOLTIP_CONTENT_MAX_DIMENSION + ( 2 * MINIMUM_BUFFER_DISTANCE_VALUE ) ) {
				newMaxWidth = TOOLTIP_CONTENT_MAX_DIMENSION;
			} else {
				newMaxWidth = largestOffset.value - ( 2 * MINIMUM_BUFFER_DISTANCE_VALUE );
			}

			// Setup max width.
			if ( secondLargestOffset.value >= TOOLTIP_CONTENT_MAX_DIMENSION + ( 2 * MINIMUM_BUFFER_DISTANCE_VALUE ) ) {
				newMaxHeight = TOOLTIP_CONTENT_MAX_DIMENSION;
			} else {
				newMaxHeight = secondLargestOffset.value - ( 2 * MINIMUM_BUFFER_DISTANCE_VALUE );
			}

			// If the position is left.
			if ( 'left' === largestOffset.dir ) {
				leftValue = tooltipTriggerRect.left - Math.min( contentElementRect.width, newMaxWidth ) - ( 2 * MINIMUM_BUFFER_DISTANCE_VALUE );
			} else {
				leftValue = tooltipTriggerRect.bottom + MINIMUM_BUFFER_DISTANCE_VALUE;
			}

			// If the secondary position is top.
			if ( 'top' === secondLargestOffset.dir ) {
				const theBottomOffset = tooltipTriggerRectOffsets.find( ( offset ) => 'bottom' === offset.dir )?.value ?? 0;
				const availableSpaceOnBottom = theBottomOffset - MINIMUM_BUFFER_DISTANCE_VALUE;

				// Get the mid point of the triggerRect.
				const triggerMidPoint = tooltipTriggerRect.top + ( tooltipTriggerRect.height / 2 );

				// Spread from top to available bottom.
				if ( availableSpaceOnBottom <= Math.min( newMaxHeight, contentElementRect.height ) / 2 ) {
					topValue = triggerMidPoint - ( Math.min( newMaxHeight, contentElementRect.height ) / 2 ) + availableSpaceOnBottom;
				} else {
					topValue = triggerMidPoint - ( Math.min( newMaxHeight, contentElementRect.height ) / 2 );
				}
			} else {
				const theTopOffset = tooltipTriggerRectOffsets.find( ( offset ) => 'top' === offset.dir )?.value ?? 0;
				const availableSpaceOnTop = theTopOffset - MINIMUM_BUFFER_DISTANCE_VALUE;

				// Get the mid point of the triggerRect.
				const triggerMidPoint = tooltipTriggerRect.top + ( tooltipTriggerRect.height / 2 );

				// Spread from top to available top.
				if ( availableSpaceOnTop <= Math.min( newMaxHeight, contentElementRect.height ) / 2 ) {
					topValue = triggerMidPoint - ( Math.min( newMaxHeight, contentElementRect.height ) / 2 ) + availableSpaceOnTop;
				} else {
					topValue = triggerMidPoint - ( Math.min( newMaxHeight, contentElementRect.height ) / 2 );
				}
			}
		}

		// Set the direction of the tooltip.
		this.tooltipContentElement.style.maxWidth = newMaxWidth + 'px';
		this.tooltipContentElement.style.maxHeight = newMaxHeight + 'px';
		this.tooltipContentElement.style.top = topValue + 'px';
		this.tooltipContentElement.style.left = leftValue + 'px';
		this.setAttribute( 'tooltip-direction', tooltipDirection );
	}

	/**
	 * Toggles the body element's scroll.
	 *
	 * @param {Event} evt The event object.
	 */
	handleTooltipToggled( evt: Event ) {
		// Null check.
		if ( ! ( 'newState' in evt ) ) {
			// Bail.
			return;
		}

		// Check and toggle.
		if ( 'open' === evt.newState ) {
			requestAnimationFrame( this.positionTooltip.bind( this ) );
			document.body.classList.add( 'prevent-scroll' );
		} else {
			document.body.classList.remove( 'prevent-scroll' );
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-tooltip', Tooltip );
