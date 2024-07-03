/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
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
import icons from '../icons';

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
import * as megaMenuItemContent from './menu-item-content';

/**
 * Register child blocks.
 */
registerBlockType( megaMenuItemContent.name, megaMenuItemContent.settings );

/**
 * Block name.
 */
export const name: string = 'quark/header-menu-item';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Header Menu Item', 'qrk' ),
	description: __( 'Individual Menu Item for Header', 'qrk' ),
	parent: [ 'quark/header-mega-menu', 'quark/secondary-nav' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [
		__( 'menu', 'qrk' ),
		__( 'item', 'qrk' ),
	],
	attributes: {
		title: {
			type: 'string',
			default: '',
		},
		hasUrl: {
			type: 'boolean',
			default: false,
		},
		url: {
			type: 'object',
			default: null,
		},
		hasIcon: {
			type: 'boolean',
			default: false,
		},
		icon: {
			type: 'string',
			default: 'search',
		},
		placeholder: {
			type: 'string',
			default: '',
		},
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
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

			// Get the closest sibling dropdown element.
			const closestDropdownElement = menuItem.nextElementSibling;

			// Check if dropdown element exists.
			if ( closestDropdownElement ) {
				// Get all dropdowns.
				const allDropdowns = document.querySelectorAll( `.${ closestDropdownElement?.classList[ 0 ] }` );

				// Close the closest dropdown element if already open.
				if ( 'true' === closestDropdownElement.getAttribute( 'open' ) ) {
					closestDropdownElement.removeAttribute( 'open' );

					// Close all other dropdowns.
					allDropdowns.forEach( ( dropdownEl ) => {
						// Remove `open` attribute.
						dropdownEl.removeAttribute( 'open' );
					} );
				} else {
					// Close all other dropdowns.
					allDropdowns.forEach( ( dropdownEl ) => {
						// Remove `open` attribute.
						dropdownEl.removeAttribute( 'open' );
					} );

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
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
