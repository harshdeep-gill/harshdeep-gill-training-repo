/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { Icon } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child blocks.
 */
import * as megaMenuItemContent from '../menu-item-content';
import * as SearchFiltersBar from '../../../search-filters-bar';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Set block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'header__nav-item', 'header__search-item' ),
	} );

	// Set inner block props.
	const innerBlockProps = useInnerBlocksProps( { className: 'header__nav-item-dropdown-content-wrap' },
		{
			allowedBlocks: [ megaMenuItemContent.name ],
			template: [ [ megaMenuItemContent.name, {},
				[
					[
						SearchFiltersBar.name,
					],
				],
			] ],
			templateLock: 'all',
		}
	);

	/**
	 * Open the closest dropdown to the menu item.
	 *
	 * @param {any} event Event.
	 */
	const openDropdown = ( event: any ) => {
		// Current menu item.
		const menuItem: HTMLElement = event.currentTarget;

		// Check if menuItem exists.
		if ( ! menuItem ) {
			// Bail early.
			return;
		}

		// Close all dropdowns.
		const closeAllDropdowns = ( allDropdowns: any ) => {
			// Check if dropdowns exist.
			if ( allDropdowns ) {
				allDropdowns.forEach( ( dropdownEl: HTMLElement ) => {
					// Remove `open` attribute.
					dropdownEl.removeAttribute( 'open' );
				} );
			}
		};

		// Get the iframe container in the full site editor.
		const iframeContainer = document.querySelector( 'iframe.edit-site-visual-editor__editor-canvas' ) as HTMLIFrameElement | null;

		// Initialize.
		let iframeContainerDocument = null;

		// Get the iframe container document.
		if ( iframeContainer ) {
			iframeContainerDocument = iframeContainer?.contentDocument || iframeContainer?.contentWindow?.document;
		}

		// Get the closest sibling dropdown element.
		const closestDropdownElement = menuItem.nextElementSibling;

		// Check if dropdown element exists.
		if ( closestDropdownElement ) {
			let allDropdowns = null;

			// Dropdown Class that needs to be targeted.
			const dropdownClass = closestDropdownElement?.classList[ 0 ];

			// Get all dropdowns.
			if ( iframeContainerDocument ) {
				allDropdowns = iframeContainerDocument.querySelectorAll( `.${ dropdownClass }` );
			} else {
				allDropdowns = document.querySelectorAll( `.${ dropdownClass }` );
			}

			// Close the closest dropdown element if already open.
			if ( 'true' === closestDropdownElement.getAttribute( 'open' ) ) {
				closeAllDropdowns( allDropdowns );
			} else {
				closeAllDropdowns( allDropdowns );

				// Open the closest dropdown element.
				closestDropdownElement.setAttribute( 'open', 'true' );
			}
		}
	};

	// Return the block's markup.
	return (
		<li { ...blockProps }>
			<button className="header__nav-item-link" onClick={ openDropdown }>
				<Icon icon="search" />
			</button>
			{
				<div { ...innerBlockProps } />
			}
		</li>
	);
}
