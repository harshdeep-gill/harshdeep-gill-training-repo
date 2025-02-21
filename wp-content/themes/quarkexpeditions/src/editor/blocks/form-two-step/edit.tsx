/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	PanelBody,
	Placeholder,
	BaseControl,
	TextControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
	URLInput,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
const { gumponents } = window;

/**
 * External components.
 */
const {	ColorPaletteControl } = gumponents.components;

// Background colors.
export const colors: { [key: string]: string }[] = [
	{ name: __( 'Black', 'qrk' ), color: '#232933', slug: 'black' },
	{ name: __( 'White', 'qrk' ), color: '#FFF', slug: 'white' },
];

// Child blocks.
import * as stepOneLandingForm from './children/landing-form';
import * as stepTwoModalForm from './children/modal-form';

/**
 * External dependencies.
 */
import classnames from 'classnames';

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
			'form-two-step',
			'white' === attributes.backgroundColor ? 'form-two-step--background-white' : '',
		),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {},
		{
			allowedBlocks: [ stepOneLandingForm.name, stepTwoModalForm.name ],
			template: [
				[ stepOneLandingForm.name ],
			],
		}
	);

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Two Step Form Options', 'qrk' ) }>
					<BaseControl
						id="quark-url-control"
						label={ __( 'Thank You Page URL', 'qrk' ) }
						help={ __( 'Select the Thank You page for this form. Leave blank for none.', 'qrk' ) }
						className="quark-url-control"
					>
						<URLInput
							onChange={ ( thankYouPageUrl: string ) => setAttributes( { thankYouPageUrl } ) }
							value={ attributes.thankYouPageUrl }
						/>
					</BaseControl>
					<ColorPaletteControl
						label={ __( 'Background Color', 'qrk' ) }
						help={ __( 'Select the background color.', 'qrk' ) }
						value={ colors.find( ( color ) => color.slug === attributes.backgroundColor )?.color }
						colors={ colors.filter( ( color ) => [ 'black', 'white' ].includes( color.slug ) ) }
						onChange={ ( backgroundColor: {
							color: string;
							slug: string;
						} ): void => {
							// Set the background color attribute.
							if ( backgroundColor.slug && [ 'black', 'white' ].includes( backgroundColor.slug ) ) {
								setAttributes( { backgroundColor: backgroundColor.slug } );
							}
						} }
					/>
					<ToggleControl
						label={ __( 'Show Modal Form?', 'qrk' ) }
						checked={ attributes.showModalForm }
						help={ __( 'Is this field required to be filled?', 'qrk' ) }
						onChange={ ( showModalForm: boolean ) => setAttributes( { showModalForm } ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Two Step Form Hidden Fields', 'qrk' ) }>
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
					<TextControl
						label={ __( 'Expedition', 'qrk' ) }
						help={ __( 'Enter the value for Expedition.', 'qrk' ) }
						value={ attributes.expedition }
						onChange={ ( expedition: string ) => setAttributes( { expedition } ) }
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
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<Placeholder
					label={ __( 'Two Step Form', 'qrk' ) }
					icon="layout"
				>
					<p>{ __( 'This form will render on the front-end.', 'qrk' ) }</p>
					<div { ...innerBlockProps } />
				</Placeholder>
			</div>
		</>
	);
}
