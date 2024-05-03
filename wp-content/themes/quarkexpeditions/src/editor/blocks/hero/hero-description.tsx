/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	InnerBlocks,
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const {
	ColorPaletteControl,
} = gumponents.components;

/**
 * Block name.
 */
export const name: string = 'quark/hero-description';

// Text colors.
export const colors: { [key: string]: string }[] = [
	{ name: __( 'Black', 'qrk' ), color: '#232933', slug: 'black' },
	{ name: __( 'White', 'qrk' ), color: '#ffffff', slug: 'white' },
];

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Hero Description', 'qrk' ),
	description: __( 'Hero Description text.', 'qrk' ),
	category: 'widgets',
	keywords: [
		__( 'description', 'qrk' ),
	],
	attributes: {
		textColor: {
			type: 'string',
			default: 'black',
			enum: [ 'white', 'black' ],
		},
	},
	parent: [ 'quark/hero-content-left' ],
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'hero__description', 'white' === attributes.textColor ? 'color-context--dark' : '' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
			allowedBlocks: [ 'core/paragraph' ],
			template: [ [ 'core/paragraph', { placeholder: __( 'Write description…', 'qrk' ) } ] ],
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Description Options', 'qrk' ) }>
						<ColorPaletteControl
							label={ __( 'Description Color', 'qrk' ) }
							help={ __( 'Select the text color for the description', 'qrk' ) }
							value={ colors.find( ( color ) => color.slug === attributes.textColor )?.color }
							colors={ colors.filter( ( color ) => [ 'white', 'black' ].includes( color.slug ) ) }
							onChange={ ( textColor: {
								color: string;
								slug: string;
							} ): void => {
								// Set the background color attribute.
								if ( textColor.slug && [ 'white', 'black' ].includes( textColor.slug ) ) {
									setAttributes( { textColor: textColor.slug } );
								}
							} }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...innerBlockProps } />
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
