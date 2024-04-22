/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InspectorControls,
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

// Text colors.
export const colors: { [key: string]: string }[] = [
	{ name: __( 'Black', 'qrk' ), color: '#232933', slug: 'black' },
	{ name: __( 'White', 'qrk' ), color: '#ffffff', slug: 'white' },
];

/**
 * Block name.
 */
export const name: string = 'quark/hero-title';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Hero Title', 'qrk' ),
	description: __( 'Hero Title text.', 'qrk' ),
	category: 'widgets',
	keywords: [
		__( 'hero', 'qrk' ),
		__( 'title', 'qrk' ),
		__( 'text', 'qrk' ),
	],
	attributes: {
		title: {
			type: 'string',
			default: '',
		},
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
			className: classnames( className, 'hero__title', 'white' === attributes.textColor ? 'color-context--dark' : '' ),
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
				<RichText
					{ ...blockProps }
					tagName="h1"
					placeholder={ __( 'Write the Title…', 'qrk' ) }
					value={ attributes.title }
					onChange={ ( title: string ) => setAttributes( { title } ) }
					allowedFormats={ [] }
				/>
			</>
		);
	},
	save() {
		// Save inner block content.
		return null;
	},
};
