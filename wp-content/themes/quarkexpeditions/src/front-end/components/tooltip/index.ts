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
	private tooltipArrowElement: HTMLElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.tooltipContentElement = this.querySelector( '.tooltip__description' );
		this.tooltipArrowElement = this.querySelector( '.tooltip__arrow' );

		// Events
		this.tooltipContentElement?.addEventListener( 'toggle', this.handleTooltipToggled.bind( this ) );
		this.addEventListener( 'mouseenter', () => this.tooltipContentElement?.showPopover() );
		this.addEventListener( 'mouseleave', () => this.tooltipContentElement?.hidePopover() );
	}

	/**
	 * Postion of tooltip.
	 */
	positionTooltip() {
		// Check if tooltip is available.
		if ( ! this.tooltipContentElement || ! this.tooltipArrowElement ) {
			// Retrun if the tooltip content is not available.
			return;
		}

		// Get the rect of the tooltip.
		const tooltipTriggerRect = this.getBoundingClientRect();

		// Horizontal Offsets.
		const tooltipTriggerHorizontalOffsets = [
			{ dir: 'left', value: tooltipTriggerRect.left },
			{ dir: 'right', value: window.innerWidth - tooltipTriggerRect.right },
		].sort( ( a, b ) => b.value - a.value ) as { dir: 'left'|'right', value: number }[];

		// Vertical Offsets
		const tooltipTriggerVerticalOffsets = [
			{ dir: 'top', value: tooltipTriggerRect.top },
			{ dir: 'bottom', value: window.innerHeight - tooltipTriggerRect.bottom },
		].sort( ( a, b ) => b.value - a.value ) as { dir: 'top'|'bottom', value: number }[];

		// Primary offset name.
		let primaryOffsetName: 'horizontal'|'vertical';

		// Determine the primary axis and get the positioning data.
		if ( tooltipTriggerHorizontalOffsets[ 0 ].value > tooltipTriggerVerticalOffsets[ 0 ].value ) {
			primaryOffsetName = 'horizontal';
		} else {
			primaryOffsetName = 'vertical';
		}

		// Get the positioning data.
		const positionData = this.getTooltipPositionData( tooltipTriggerHorizontalOffsets, tooltipTriggerVerticalOffsets, primaryOffsetName );

		// Null check.
		if ( ! positionData ) {
			// Bail.
			return;
		}

		// Get the values.
		const { newMaxHeight, newMaxWidth, topValue, leftValue, tooltipDirection, tooltipArrowPosition } = positionData;

		// Set the direction of the tooltip.
		this.tooltipContentElement.style.maxWidth = newMaxWidth + 'px';
		this.tooltipContentElement.style.maxHeight = newMaxHeight + 'px';
		this.tooltipContentElement.style.top = topValue + 'px';
		this.tooltipContentElement.style.left = leftValue + 'px';
		this.setAttribute( 'tooltip-direction', tooltipDirection );

		// Arrow position.
		const arrowPositionValue = tooltipArrowPosition.value + 'px';

		// Tooltip arrow positioning.
		if ( 'top' === tooltipArrowPosition.type ) {
			this.tooltipArrowElement.style.left = '';
			this.tooltipArrowElement.style.top = arrowPositionValue;
		} else {
			this.tooltipArrowElement.style.top = '';
			this.tooltipArrowElement.style.left = arrowPositionValue;
		}
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
			document.body.classList.add( 'prevent-scroll' );
			requestAnimationFrame( this.positionTooltip.bind( this ) );
		} else {
			this.removeAttribute( 'tooltip-direction' );
			document.body.classList.remove( 'prevent-scroll' );
		}
	}

	/**
	 * Returns the positioning data for tooltip.
	 *
	 * @param { Object[] } tooltipTriggerHorizontalOffsets
	 * @param { Object[] } tooltipTriggerVerticalOffsets
	 * @param { string }   primaryOffsetName
	 *
	 * @return { Object|undefined } The data.
	 */
	getTooltipPositionData(
		tooltipTriggerHorizontalOffsets: { dir: 'left'|'right', value: number }[],
		tooltipTriggerVerticalOffsets: { dir: 'top'|'bottom', value: number }[],
		primaryOffsetName: 'horizontal'|'vertical'
	) {
		// Sanity check.
		if (
			! this.tooltipContentElement ||
			tooltipTriggerHorizontalOffsets.length !== 2 ||
			tooltipTriggerVerticalOffsets.length !== 2
		) {
			// Bail.
			return;
		}

		// Maximum value for height/width.
		const TOOLTIP_CONTENT_MAX_DIMENSION = 360;

		// The distance between content and screen edges / icon and content.
		const MINIMUM_BUFFER_DISTANCE_VALUE = 20;

		// Tooltip direction.
		const tooltipDirection = 'vertical' === primaryOffsetName ? tooltipTriggerVerticalOffsets[ 0 ].dir : tooltipTriggerHorizontalOffsets[ 0 ].dir;

		// Add class to the arrow.
		this.tooltipArrowElement?.setAttribute( 'data-direction', tooltipDirection );

		// Get the offsets.
		const triggerTopOffset = tooltipTriggerVerticalOffsets.find( ( offset ) => 'top' === offset.dir )?.value;
		const triggerBottomOffset = tooltipTriggerVerticalOffsets.find( ( offset ) => 'bottom' === offset.dir )?.value;
		const triggerLeftOffset = tooltipTriggerHorizontalOffsets.find( ( offset ) => 'left' === offset.dir )?.value;
		const triggerRightOffset = tooltipTriggerHorizontalOffsets.find( ( offset ) => 'right' === offset.dir )?.value;

		// Null check
		if ( ! (
			triggerTopOffset &&
			triggerBottomOffset &&
			triggerLeftOffset &&
			triggerRightOffset
		) ) {
			// Bail.
			return;
		}

		// Get the rect for content element.
		const triggerRect = this.getBoundingClientRect();
		const contentRect = this.tooltipContentElement.getBoundingClientRect();

		// New max height.
		let availableVerticalSpace = -2 * MINIMUM_BUFFER_DISTANCE_VALUE;

		// Calculate the available space.
		if ( 'vertical' === primaryOffsetName ) {
			availableVerticalSpace += tooltipTriggerVerticalOffsets[ 0 ].value;
		} else {
			availableVerticalSpace += tooltipTriggerVerticalOffsets[ 0 ].value + tooltipTriggerVerticalOffsets[ 1 ].value + triggerRect.height;
		}

		// Assign height accordingly.
		const newMaxHeight = Math.min( availableVerticalSpace, TOOLTIP_CONTENT_MAX_DIMENSION );

		// top
		let topValue = MINIMUM_BUFFER_DISTANCE_VALUE;
		const desiredContentHeight = Math.min( newMaxHeight, contentRect.height );
		const triggerCenterOffsetTop = triggerTopOffset + ( triggerRect.height / 2 ); // top offset for the center of trigger.
		const triggerCenterOffsetBottom = triggerBottomOffset + ( triggerRect.height / 2 ); // bottom offset for the center of trigger.

		// Check the direction and compute values.
		if ( 'top' === tooltipDirection ) {
			// Set the top value.
			topValue = triggerTopOffset - desiredContentHeight - MINIMUM_BUFFER_DISTANCE_VALUE;
		} else if ( 'bottom' === tooltipDirection ) {
			topValue = triggerRect.bottom + MINIMUM_BUFFER_DISTANCE_VALUE;
		} else if ( [ 'left', 'right' ].includes( tooltipDirection ) ) {
			// If the main direction of the tooltip is horizontal, try to spread the content across top and bottom.
			if ( triggerCenterOffsetTop <= desiredContentHeight / 2 ) {
				topValue = MINIMUM_BUFFER_DISTANCE_VALUE;
			} else if ( triggerCenterOffsetBottom <= desiredContentHeight / 2 ) {
				topValue = window.innerHeight - desiredContentHeight - MINIMUM_BUFFER_DISTANCE_VALUE;
			} else {
				topValue = triggerCenterOffsetTop - ( desiredContentHeight / 2 ) + MINIMUM_BUFFER_DISTANCE_VALUE;
			}
		}

		// New max width
		let availableHorizontalSpace = -2 * MINIMUM_BUFFER_DISTANCE_VALUE;

		// Calculate the available space.
		if ( 'vertical' === primaryOffsetName ) {
			availableHorizontalSpace += tooltipTriggerHorizontalOffsets[ 0 ].value + tooltipTriggerHorizontalOffsets[ 1 ].value + triggerRect.width;
		} else {
			availableHorizontalSpace += tooltipTriggerHorizontalOffsets[ 0 ].value;
		}

		// Assign new max width accordingly.
		const newMaxWidth = Math.min( availableHorizontalSpace, TOOLTIP_CONTENT_MAX_DIMENSION );

		// left
		let leftValue = MINIMUM_BUFFER_DISTANCE_VALUE;
		const desiredContentWidth = Math.min( newMaxWidth, contentRect.width );
		const triggerCenterOffsetLeft = triggerLeftOffset + ( triggerRect.width / 2 ); // left offset for the center of trigger.
		const triggerCenterOffsetRight = triggerRightOffset + ( triggerRect.width / 2 ); // right offset for the center of trigger.

		// Check the direction and compute values.
		if ( 'left' === tooltipDirection ) {
			// Set the left value.
			leftValue = triggerLeftOffset - desiredContentWidth - MINIMUM_BUFFER_DISTANCE_VALUE;
		} else if ( 'right' === tooltipDirection ) {
			leftValue = triggerRect.right + MINIMUM_BUFFER_DISTANCE_VALUE;
		} else if ( [ 'top', 'bottom' ].includes( tooltipDirection ) ) {
			// If the main direction of the tooltip is horizontal, try to spread the content across left and right.
			if ( triggerCenterOffsetLeft <= desiredContentWidth / 2 ) {
				leftValue = MINIMUM_BUFFER_DISTANCE_VALUE;
			} else if ( triggerCenterOffsetRight <= desiredContentWidth / 2 ) {
				leftValue = window.innerWidth - desiredContentWidth - MINIMUM_BUFFER_DISTANCE_VALUE;
			} else {
				leftValue = triggerCenterOffsetLeft - ( desiredContentWidth / 2 ) - MINIMUM_BUFFER_DISTANCE_VALUE;
			}
		}

		// Tooltip arrow positioning.
		const tooltipArrowPosition = {
			type: '',
			value: 0,
		};
		const TOOLTIP_ARROW_DIMENSION = 9;

		// Check and add positioning for tooltip arrow.
		if ( [ 'left', 'right' ].includes( tooltipDirection ) ) {
			tooltipArrowPosition.type = 'top';
			tooltipArrowPosition.value = triggerCenterOffsetTop - topValue - TOOLTIP_ARROW_DIMENSION;
		} else {
			tooltipArrowPosition.type = 'left';
			tooltipArrowPosition.value = triggerCenterOffsetLeft - leftValue - TOOLTIP_ARROW_DIMENSION;
		}

		// Return the values.
		return { newMaxWidth, newMaxHeight, topValue, leftValue, tooltipDirection, tooltipArrowPosition };
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-tooltip', Tooltip );
