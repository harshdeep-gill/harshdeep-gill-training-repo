/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

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
		className: classnames(
			className,
			'hero__sub-title',
			'white' === attributes.textColor ? 'color-context--dark' : '',
			attributes.usePromoFont ? 'font-family--promo' : '',
		),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Subtitle Options', 'qrk' ) }>
					<ColorPaletteControl
						label={ __( 'Subtitle Color', 'qrk' ) }
						help={ __( 'Select the text color for the subtitle', 'qrk' ) }
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
					<ToggleControl
						label={ __( 'Use Promo Font', 'qrk' ) }
						checked={ attributes.usePromoFont }
						onChange={ ( usePromoFont ) => setAttributes( { usePromoFont } ) }
						help={ __( 'Should this text be in the Promo Font?', 'qrk' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<RichText
				{ ...blockProps }
				tagName="h5"
				placeholder={ __( 'Write the Subtitle…', 'qrk' ) }
				value={ attributes.subtitle }
				onChange={ ( subtitle: string ) => setAttributes( { subtitle } ) }
				allowedFormats={ [] }
			/>
		</>
	);
}
