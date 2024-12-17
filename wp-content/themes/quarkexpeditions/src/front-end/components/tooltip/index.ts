/**
 * Utility functions.
 */
import { throttle } from '../../global/utility';

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
	private tooltipPopoverElement: HTMLElement | null;
	private tooltipArrowElement: HTMLElement | null;
	private tooltipContentElement: HTMLElement | null;
	private readonly MINIMUM_BUFFER_DISTANCE_VALUE = 20;
	private readonly TOOLTIP_POPOVER_MAX_DIMENSION = 360;
	private subscribedEventListeners: { element: EventTarget, event: string, handler: EventListenerOrEventListenerObject }[];

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.tooltipPopoverElement = this.querySelector( '.tooltip__description' );
		this.tooltipArrowElement = this.querySelector( '.tooltip__arrow' );
		this.tooltipContentElement = this.querySelector( '.tooltip__description-content' );
		this.subscribedEventListeners = [];

		// Events
		this.tooltipPopoverElement?.addEventListener( 'toggle', this.handleTooltipToggled.bind( this ) );
		this.tooltipPopoverElement?.addEventListener( 'beforetoggle', this.handleBeforeToggled.bind( this ) );

		// Setup pointer events. We only need to do this in case of a mouse. Touch will work without it.
		this.addEventListener( 'pointerenter', ( evt: PointerEvent ) => {
			// Check if it was a mouse and proceed accordingly.
			if ( 'mouse' === evt.pointerType && ! this.tooltipPopoverElement?.matches( ':popover-open' ) ) {
				this.tooltipPopoverElement?.showPopover();
			}
		} );
		this.addEventListener( 'pointerleave', ( evt: PointerEvent ) => {
			// Check and hide popover.
			if ( 'mouse' === evt.pointerType && this.tooltipPopoverElement?.matches( ':popover-open' ) ) {
				this.tooltipPopoverElement?.hidePopover();
			}
		} );
	}

	/**
	 * Connected Callback
	 */
	connectedCallback() {
		// Adding it here because when it was being added in the constructor, there were some instances where the object was created but not connected to the dom causing dangling listeners.
		if ( this.tooltipPopoverElement && this.tooltipContentElement && typeof this.tooltipPopoverElement.hidePopover === 'function' ) {
			const throttledListener = throttle( this.handleWindowScroll.bind( this ), 2000 );
			window.addEventListener( 'scroll', throttledListener, true );

			// Add into subscribed event listeners
			this.subscribedEventListeners.push( { element: window, event: 'scroll', handler: throttledListener } );
		}
	}

	/**
	 * Disconnected Callback.
	 */
	disconnectedCallback() {
		// Loop through the listeners
		this.subscribedEventListeners.forEach( ( entry ) => {
			// Remove the event listeners.
			entry.element.removeEventListener( entry.event, entry.handler, true );
		} );
	}

	/**
	 * Handles the scroll outside of the tooltip.
	 *
	 * @param { Event } evt The event object.
	 */
	handleWindowScroll( evt: Event ) {
		// If the content was the original target.
		if ( evt.target === this.tooltipContentElement ) {
			// Do nothing.
			return;
		}

		// Hide the popover
		if ( this.tooltipPopoverElement?.matches( ':popover-open' ) ) {
			this.tooltipPopoverElement?.hidePopover();
		}
	}

	/**
	 * Position of tooltip.
	 */
	positionTooltip() {
		// Check if tooltip is available.
		if ( ! this.tooltipPopoverElement || ! this.tooltipArrowElement || ! this.tooltipContentElement ) {
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

			// Check if there is enough space in the vertical axis.
			if ( tooltipTriggerVerticalOffsets[ 1 ].value <= this.MINIMUM_BUFFER_DISTANCE_VALUE ) {
				primaryOffsetName = 'vertical';
			}
		} else {
			primaryOffsetName = 'vertical';

			// Check if there is enough space in the horizontal axis.
			if ( tooltipTriggerHorizontalOffsets[ 1 ].value <= this.MINIMUM_BUFFER_DISTANCE_VALUE ) {
				primaryOffsetName = 'horizontal';
			}
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
		this.tooltipPopoverElement.style.maxWidth = newMaxWidth + 'px';
		this.tooltipPopoverElement.style.maxHeight = newMaxHeight + 'px';
		this.tooltipPopoverElement.style.top = topValue + 'px';
		this.tooltipPopoverElement.style.left = leftValue + 'px';
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

		// Make the tooltip visible.
		this.tooltipPopoverElement.style.visibility = '';
	}

	/**
	 * Toggles the body element's scroll.
	 *
	 * @param {Event} evt The event object.
	 */
	handleTooltipToggled( evt: Event ) {
		// Null check.
		if ( ! ( 'newState' in evt ) || ! this.tooltipPopoverElement ) {
			// Bail.
			return;
		}

		// Check and toggle.
		if ( 'open' === evt.newState ) {
			// Prevent body scroll.
			requestAnimationFrame( this.positionTooltip.bind( this ) );

			/**
			 * We need to do this for polyfilled browsers because popover-polyfill deletes any CSS
			 * that contains any styles with class `.\:popover-open`
			 * ref: https://github.com/oddbird/popover-polyfill?tab=readme-ov-file#caveats
			 */
			if ( this.tooltipPopoverElement.classList.contains( ':popover-open' ) ) {
				this.tooltipPopoverElement.style.display = 'flex';
			}
		} else {
			this.removeAttribute( 'tooltip-direction' );

			/**
			 * We need to do this for polyfilled browsers because popover-polyfill deletes any CSS
			 * that contains any styles with class `.\:popover-open`
			 * ref: https://github.com/oddbird/popover-polyfill?tab=readme-ov-file#caveats
			 */
			this.tooltipPopoverElement.style.display = '';
		}
	}

	/**
	 * Toggles the body element's scroll.
	 *
	 * @param {Event} evt The event object.
	 */
	handleBeforeToggled( evt: Event ) {
		// Null check.
		if ( ! ( 'newState' in evt ) || ! this.tooltipPopoverElement ) {
			// Bail.
			return;
		}

		// Check and toggle.
		if ( 'open' === evt.newState ) {
			// Hide the tooltip
			this.tooltipPopoverElement.style.visibility = 'hidden';
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
			! this.tooltipPopoverElement ||
			tooltipTriggerHorizontalOffsets.length !== 2 ||
			tooltipTriggerVerticalOffsets.length !== 2
		) {
			// Bail.
			return;
		}

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

		// Get the rect for popover element.
		const triggerRect = this.getBoundingClientRect();
		const popoverRect = this.tooltipPopoverElement.getBoundingClientRect();

		// New max height.
		let availableVerticalSpace = -2 * this.MINIMUM_BUFFER_DISTANCE_VALUE;

		// Calculate the available space.
		if ( 'vertical' === primaryOffsetName ) {
			availableVerticalSpace += tooltipTriggerVerticalOffsets[ 0 ].value;
		} else {
			availableVerticalSpace += tooltipTriggerVerticalOffsets[ 0 ].value + tooltipTriggerVerticalOffsets[ 1 ].value + triggerRect.height;
		}

		// Assign height accordingly.
		const newMaxHeight = Math.min( availableVerticalSpace, this.TOOLTIP_POPOVER_MAX_DIMENSION );

		// top
		let topValue = this.MINIMUM_BUFFER_DISTANCE_VALUE;
		const desiredPopoverHeight = Math.min( newMaxHeight, popoverRect.height );
		const triggerCenterOffsetTop = triggerTopOffset + ( triggerRect.height / 2 ); // top offset for the center of trigger.
		const triggerCenterOffsetBottom = triggerBottomOffset + ( triggerRect.height / 2 ); // bottom offset for the center of trigger.

		// Check the direction and compute values.
		if ( 'top' === tooltipDirection ) {
			// Set the top value.
			topValue = triggerTopOffset - desiredPopoverHeight - this.MINIMUM_BUFFER_DISTANCE_VALUE;
		} else if ( 'bottom' === tooltipDirection ) {
			topValue = triggerRect.bottom + this.MINIMUM_BUFFER_DISTANCE_VALUE;
		} else if ( [ 'left', 'right' ].includes( tooltipDirection ) ) {
			// If the main direction of the tooltip is horizontal, try to spread the popover across top and bottom.
			if ( triggerCenterOffsetTop <= desiredPopoverHeight / 2 ) {
				topValue = this.MINIMUM_BUFFER_DISTANCE_VALUE;
			} else if ( triggerCenterOffsetBottom <= desiredPopoverHeight / 2 ) {
				topValue = window.innerHeight - desiredPopoverHeight - this.MINIMUM_BUFFER_DISTANCE_VALUE;
			} else {
				topValue = triggerCenterOffsetTop - ( desiredPopoverHeight / 2 );
			}
		}

		// New max width
		let availableHorizontalSpace = -2 * this.MINIMUM_BUFFER_DISTANCE_VALUE;

		// Calculate the available space.
		if ( 'vertical' === primaryOffsetName ) {
			availableHorizontalSpace += tooltipTriggerHorizontalOffsets[ 0 ].value + tooltipTriggerHorizontalOffsets[ 1 ].value + triggerRect.width;
		} else {
			availableHorizontalSpace += tooltipTriggerHorizontalOffsets[ 0 ].value;
		}

		// Assign new max width accordingly.
		const newMaxWidth = Math.min( availableHorizontalSpace, this.TOOLTIP_POPOVER_MAX_DIMENSION );

		// left
		let leftValue = this.MINIMUM_BUFFER_DISTANCE_VALUE;
		const desiredPopoverWidth = Math.min( newMaxWidth, popoverRect.width );
		const triggerCenterOffsetLeft = triggerLeftOffset + ( triggerRect.width / 2 ); // left offset for the center of trigger.
		const triggerCenterOffsetRight = triggerRightOffset + ( triggerRect.width / 2 ); // right offset for the center of trigger.

		// Check the direction and compute values.
		if ( 'left' === tooltipDirection ) {
			// Set the left value.
			leftValue = triggerLeftOffset - desiredPopoverWidth - this.MINIMUM_BUFFER_DISTANCE_VALUE;
		} else if ( 'right' === tooltipDirection ) {
			leftValue = triggerRect.right + this.MINIMUM_BUFFER_DISTANCE_VALUE;
		} else if ( [ 'top', 'bottom' ].includes( tooltipDirection ) ) {
			// If the main direction of the tooltip is horizontal, try to spread the popover across left and right.
			if ( triggerCenterOffsetLeft <= desiredPopoverWidth / 2 ) {
				leftValue = this.MINIMUM_BUFFER_DISTANCE_VALUE;
			} else if ( triggerCenterOffsetRight <= desiredPopoverWidth / 2 ) {
				leftValue = window.innerWidth - desiredPopoverWidth - this.MINIMUM_BUFFER_DISTANCE_VALUE;
			} else {
				leftValue = triggerCenterOffsetLeft - ( desiredPopoverWidth / 2 );
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
