/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
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

// Text colors.
export const colors: { [key: string]: string }[] = [
	{ name: __( 'Blue', 'qrk' ), color: '#4c8bbf', slug: 'blue' },
	{ name: __( 'Black', 'qrk' ), color: '#232933', slug: 'black' },
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
			'search-hero__overline',
			'overline',
			attributes.textColor ? `search-hero__overline-color--${ attributes.textColor }` : '',
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
}
