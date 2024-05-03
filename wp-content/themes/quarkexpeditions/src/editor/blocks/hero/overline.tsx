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
import {
	PanelBody,
} from '@wordpress/components';

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
export const name: string = 'quark/hero-overline';

// Text colors.
export const colors: { [key: string]: string }[] = [
	{ name: __( 'Blue', 'qrk' ), color: '#4c8bbf', slug: 'blue' },
	{ name: __( 'Black', 'qrk' ), color: '#232933', slug: 'black' },
];

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Hero Overline', 'qrk' ),
	description: __( 'Overline text.', 'qrk' ),
	category: 'widgets',
	keywords: [
		__( 'hero', 'qrk' ),
		__( 'overline', 'qrk' ),
		__( 'text', 'qrk' ),
	],
	attributes: {
		overline: {
			type: 'string',
			default: '',
		},
		textColor: {
			type: 'string',
			default: 'blue',
			enum: [ 'blue', 'black' ],
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
			className: classnames(
				className,
				'hero__overline',
				'overline',
				attributes.textColor ? `hero__overline-color--${ attributes.textColor }` : '',
			),
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Overline Options', 'qrk' ) }>
						<ColorPaletteControl
							label={ __( 'Overline Color', 'qrk' ) }
							help={ __( 'Select the text color for the overline', 'qrk' ) }
							value={ colors.find( ( color ) => color.slug === attributes.textColor )?.color }
							colors={ colors.filter( ( color ) => [ 'blue', 'black' ].includes( color.slug ) ) }
							onChange={ ( textColor: {
								color: string;
								slug: string;
							} ): void => {
								// Set the background color attribute.
								if ( textColor.slug && [ 'blue', 'black' ].includes( textColor.slug ) ) {
									setAttributes( { textColor: textColor.slug } );
								}
							} }
						/>
					</PanelBody>
				</InspectorControls>
				<RichText
					{ ...blockProps }
					tagName="span"
					placeholder={ __( 'Write overline text…', 'qrk' ) }
					value={ attributes.overline }
					onChange={ ( overline: string ) => setAttributes( { overline } ) }
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
