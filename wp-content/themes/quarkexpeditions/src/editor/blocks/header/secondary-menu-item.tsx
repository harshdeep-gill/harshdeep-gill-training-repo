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
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

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
export const name: string = 'quark/secondary-menu-item';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Header Menu Item', 'qrk' ),
	description: __( 'Individual Menu Item for Header', 'qrk' ),
	parent: [ 'quark/secondary-nav' ],
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
		url: {
			type: 'object',
			default: null,
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

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Header Menu Item Options', 'qrk' ) }>
						<LinkControl
							label={ __( 'Select URL', 'qrk' ) }
							value={ attributes.url }
							help={ __( 'Enter an URL for this item', 'qrk' ) }
							onChange={ ( url: object ) => setAttributes( { url } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<li { ...blockProps }>
					{
						<RichText
							tagName="a"
							className="header__nav-item-link"
							placeholder={ attributes.placeholder || __( 'Menu Item…', 'qrk' ) }
							value={ attributes.title }
							onChange={ ( title: string ) => setAttributes( { title } ) }
							allowedFormats={ [] }
						/>
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
