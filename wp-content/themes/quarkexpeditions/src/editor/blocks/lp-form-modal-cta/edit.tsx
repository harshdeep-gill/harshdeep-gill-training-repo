/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	SelectControl,
} from '@wordpress/components';

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
const { ColorPaletteControl } = gumponents.components;

// Background colors.
export const colors: { [key: string]: string }[] = [
	{ name: __( 'Yellow', 'qrk' ), color: '#fdb52b', slug: 'yellow' },
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
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( { className: classnames( className, 'lp-form-modal-cta' ) } );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'LP Form Modal CTA Options', 'qrk' ) }>
					<ColorPaletteControl
						label={ __( 'Background Color', 'qrk' ) }
						help={ __( 'Select the background color.', 'qrk' ) }
						value={ colors.find( ( color ) => color.slug === attributes.backgroundColor )?.color }
						colors={ colors.filter( ( color ) => [ 'black', 'yellow' ].includes( color.slug ) ) }
						onChange={ ( backgroundColor: {
							color: string;
							slug: string;
						} ): void => {
							// Set the background color attribute.
							if ( backgroundColor.slug && [ 'black', 'yellow' ].includes( backgroundColor.slug ) ) {
								setAttributes( { backgroundColor: backgroundColor.slug } );
							}
						} }
					/>
				</PanelBody>
				<PanelBody title={ __( 'LP Form Modal CTA Hidden Fields', 'qrk' ) }>
					<SelectControl
						label={ __( 'Polar Region', 'qrk' ) }
						help={ __( 'Select the value for Polar Region.', 'qrk' ) }
						value={ attributes.polarRegion }
						options={ [
							{ label: __( 'Select Polar Region…', 'qrk' ), value: '' },
							{ label: __( 'Arctic(ARC)', 'qrk' ), value: 'ARC' },
							{ label: __( 'Antarctic(ANT)', 'qrk' ), value: 'ANT' },
						] }
						onChange={ ( polarRegion: string ) => setAttributes( { polarRegion } ) }
					/>
					<SelectControl
						label={ __( 'Season', 'qrk' ) }
						help={ __( 'Select the value for Season.', 'qrk' ) }
						value={ attributes.season }
						options={ [
							{ label: __( 'Select season…', 'qrk' ), value: '' },
							{ label: __( '2024', 'qrk' ), value: '2024' },
							{ label: __( '2024-25', 'qrk' ), value: '2024-25' },
							{ label: __( '2025', 'qrk' ), value: '2025' },
							{ label: __( '2025-26', 'qrk' ), value: '2025-26' },
							{ label: __( '2026', 'qrk' ), value: '2026' },
							{ label: __( '2026-27', 'qrk' ), value: '2026-27' },
						] }
						onChange={ ( season: string ) => setAttributes( { season } ) }
					/>
					<SelectControl
						label={ __( 'Ship', 'qrk' ) }
						help={ __( 'Select the value for Ship.', 'qrk' ) }
						value={ attributes.ship }
						options={ [
							{ label: __( 'Select Ship…', 'qrk' ), value: '' },
							{ label: __( 'ULT', 'qrk' ), value: 'ULT' },
							{ label: __( 'WEX', 'qrk' ), value: 'WEX' },
							{ label: __( 'OEX', 'qrk' ), value: 'OEX' },
							{ label: __( 'WVO', 'qrk' ), value: 'WVO' },
						] }
						onChange={ ( ship: string ) => setAttributes( { ship } ) }
					/>
					<SelectControl
						label={ __( 'Sub Region', 'qrk' ) }
						help={ __( 'Select the value for Sub Region.', 'qrk' ) }
						value={ attributes.subRegion }
						options={ [
							{ label: __( 'Select Sub Region…', 'qrk' ), value: '' },
							{ label: __( 'Antarctic Peninsula', 'qrk' ), value: 'Antarctic Peninsula' },
							{ label: __( 'Falklands & South Georgia', 'qrk' ), value: 'Falklands & South Georgia' },
							{ label: __( 'Patagonia', 'qrk' ), value: 'Patagonia' },
							{ label: __( 'Snow Hill Island', 'qrk' ), value: 'Snow Hill Island' },
							{ label: __( 'Greenland', 'qrk' ), value: 'Greenland' },
							{ label: __( 'Svalbard', 'qrk' ), value: 'Svalbard' },
							{ label: __( 'Canadian High Arctic', 'qrk' ), value: 'Canadian High Arctic' },
							{ label: __( 'North Pole', 'qrk' ), value: 'North Pole' },
							{ label: __( 'Russian High Arctic', 'qrk' ), value: 'Russian High Arctic' },
							{ label: __( 'Iceland', 'qrk' ), value: 'Iceland' },
						] }
						onChange={ ( subRegion: string ) => setAttributes( { subRegion } ) }
					/>
					<TextControl
						label={ __( 'Expedition', 'qrk' ) }
						help={ __( 'Enter the value for Expedition.', 'qrk' ) }
						value={ attributes.expedition }
						onChange={ ( expedition: string ) => setAttributes( { expedition } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<RichText
					tagName="span"
					className={ classnames( 'btn', 'btn--size-big', 'black' === attributes.backgroundColor ? 'btn--color-black' : '', ) }
					placeholder={ __( 'Write CTA text…', 'qrk' ) }
					value={ attributes.text }
					onChange={ ( text: string ) => setAttributes( { text } ) }
					allowedFormats={ [] }
				/>
			</div>
		</>
	);
}
