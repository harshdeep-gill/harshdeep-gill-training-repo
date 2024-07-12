/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InnerBlocks,
	RichText,
	InspectorControls,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { Icon, PanelBody, SelectControl, ToggleControl } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import icons from '../../../icons';

/**
 * External dependencies.
 */
const { gumponents } = window;

/**
 * External components.
 */
const { LinkControl } = gumponents.components;

/**
 * Child blocks.
 */
import * as megaMenuItemContent from '../menu-item-content';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'header__nav-item' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { className: 'header__nav-item-dropdown-content-wrap' },
		{
			allowedBlocks: [ megaMenuItemContent.name ],
			template: [ [ megaMenuItemContent.name ] ],
			renderAppender: InnerBlocks.DefaultBlockAppender,

			// @ts-ignore
			orientation: 'horizontal',
		}
	);

	// Prepare icon.
	let selectedIcon: any = '';

	// Set icon.
	if ( attributes.icon && '' !== attributes.icon ) {
		// Kebab-case to camel-case.
		const iconName: string = attributes.icon.replace( /-([a-z])/g, ( _: any, group:string ) => group.toUpperCase() );
		selectedIcon = icons[ iconName ] ?? '';
	}

	// Fallback icon.
	if ( ! selectedIcon || '' === selectedIcon ) {
		selectedIcon = <Icon icon="no" />;
	}

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
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Header Menu Item Options', 'qrk' ) }>
					<ToggleControl
						label={ __( 'Has Icon?', 'qrk' ) }
						checked={ attributes.hasIcon }
						help={ __( 'Does the item have an icon?', 'qrk' ) }
						onChange={ ( hasIcon: boolean ) => setAttributes( { hasIcon, hasUrl: ! hasIcon } ) }
					/>
					{
						! attributes.hasIcon &&
						<ToggleControl
							label={ __( 'Has URL?', 'qrk' ) }
							checked={ attributes.hasUrl }
							help={ __( 'Does clicking this item direct to a hyperlink?', 'qrk' ) }
							onChange={ ( hasUrl: boolean ) => setAttributes( { hasUrl } ) }
						/>
					}
					{
						( ! attributes.hasIcon && attributes.hasUrl ) &&
						<LinkControl
							label={ __( 'Select URL', 'qrk' ) }
							value={ attributes.url }
							help={ __( 'Enter an URL for this item', 'qrk' ) }
							onChange={ ( url: object ) => setAttributes( { url } ) }
						/>
					}
					{
						attributes.hasIcon &&
						<SelectControl
							label={ __( 'Icon', 'qrk' ) }
							help={ __( 'Select the icon.', 'qrk' ) }
							value={ attributes.icon }
							options={ [
								{ label: __( 'Select Icon…', 'qrk' ), value: '' },
								{ label: __( 'Search', 'qrk' ), value: 'search' },
							] }
							onChange={ ( icon: string ) => setAttributes( { icon } ) }
						/>
					}
				</PanelBody>
			</InspectorControls>
			<li { ...blockProps }>
				{
					! attributes.hasIcon
						? (
							<RichText
								tagName="a"
								className="header__nav-item-link"
								placeholder={ attributes.placeholder || __( 'Menu Item…', 'qrk' ) }
								value={ attributes.title }
								onChange={ ( title: string ) => setAttributes( { title } ) }
								allowedFormats={ [] }
								onClick={ openDropdown }
							/>
						) : (
							<button className="header__nav-item-link" onClick={ openDropdown }>
								{ selectedIcon }
							</button>
						)
				}
				{
					! attributes.hasUrl &&
					<div { ...innerBlockProps } />
				}
			</li>
		</>
	);
}
