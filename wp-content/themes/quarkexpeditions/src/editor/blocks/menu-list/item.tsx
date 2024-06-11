/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * External components.
 */
const { gumponents } = window;
const { LinkControl } = gumponents.components;

/**
 * Block name.
 */
export const name: string = 'quark/menu-list-item';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Menu List Item', 'qrk' ),
	description: __( 'Individual menu list item', 'qrk' ),
	parent: [ 'quark/menu-list' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'menu', 'qrk' ), __( 'list', 'qrk' ) ],
	attributes: {
		title: {
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
	edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'menu-list__item' ),
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Menu List Options', 'qrk' ) }>
						<LinkControl
							label={ __( 'Select URL', 'qrk' ) }
							value={ attributes.url }
							help={ __( 'Enter an URL for this item', 'qrk' ) }
							onChange={ ( url: object ) => setAttributes( { url } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<li { ...blockProps }>
					<RichText
						tagName="a"
						className="maybe-link menu-list__item-link"
						placeholder={ __( 'Menu List Item…', 'qrk' ) }
						value={ attributes.title }
						onChange={ ( title: string ) => setAttributes( { title } ) }
						allowedFormats={ [] }
					/>
				</li>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
