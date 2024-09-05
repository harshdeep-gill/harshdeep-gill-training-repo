/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';
import { PanelBody } from '@wordpress/components';
const { gumponents } = window;

/**
 * External components.
 */
const {	ColorPaletteControl } = gumponents.components;

// Tag colors.
export const colors: { [key: string]: string }[] = [
	{ name: __( 'Yellow', 'qrk' ), color: '#fdb52b', slug: 'yellow' },
	{ name: __( 'Magenta', 'qrk' ), color: '#a26792', slug: 'magenta' },
];

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
	// Set block attributes.
	const blocksProps = useBlockProps( {
		className: classnames(
			className,
			'info-cards__tag',
			'overline',
			'info-cards__tag--has-background-' + attributes.backgroundColor,
		),
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Tag Color', 'qrk' ) }>
					<ColorPaletteControl
						label={ __( 'Background Color', 'qrk' ) }
						help={ __( 'Select the background color.', 'qrk' ) }
						value={ colors.find( ( color ) => color.slug === attributes.backgroundColor )?.color }
						colors={ colors.filter( ( color ) => [ 'yellow', 'magenta' ].includes( color.slug ) ) }
						onChange={ ( backgroundColor: {
							color: string;
							slug: string;
						} ): void => {
							// Set the background color attribute.
							if ( backgroundColor.slug && [ 'yellow', 'magenta' ].includes( backgroundColor.slug ) ) {
								setAttributes( { backgroundColor: backgroundColor.slug } );
							}
						} }
					/>
				</PanelBody>
			</InspectorControls>
			<RichText
				{ ...blocksProps }
				tagName="h4"
				placeholder={ __( 'Write Tag', 'qrk' ) }
				value={ attributes.text }
				onChange={ ( text: string ) => setAttributes( { text } ) }
				allowedFormats={ [] }
			/>
		</>
	);
}
