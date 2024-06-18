/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	InspectorControls,
	RichText,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * Internal dependencies.
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
 * Block name.
 */
export const name: string = 'quark/footer-navigation-item';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer Navigation Item', 'qrk' ),
	description: __( 'Display a navigation item.', 'qrk' ),
	parent: [ 'quark/footer-navigation' ],
	category: 'layout',
	keywords: [
		__( 'footer', 'qrk' ),
		__( 'navigation', 'qrk' ),
		__( 'item', 'qrk' ),
	],
	attributes: {
		title: {
			type: 'string',
			default: '',
		},
		url: {
			type: 'object',
			default: {},
		},
	},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( { className: classnames( className, 'footer__navigation-item' ) } );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Navigation Item Options', 'qrk' ) }>
						<LinkControl
							label={ __( 'Select URL', 'qrk' ) }
							value={ attributes.url }
							help={ __( 'Enter an URL for this item', 'qrk' ) }
							onChange={ ( url: object ) => setAttributes( { url } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<li { ...blockProps }>
					<span className="footer__navigation-item">
						<RichText
							tagName="a"
							className="footer__navigation-item-link"
							placeholder={ __( 'Write Navigation Item Title…', 'qrk' ) }
							value={ attributes.title }
							onChange={ ( title: string ) => setAttributes( { title } ) }
							allowedFormats={ [] }
						/>
					</span>
				</li>
			</>
		);
	},
	save() {
		// Return null;
		return null;
	},
};
