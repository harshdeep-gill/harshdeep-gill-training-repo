/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	PanelBody,
	Placeholder,
	BaseControl,
	TextControl,
	SelectControl,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
	URLInput,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
const { gumponents } = window;

/**
 * External components.
 */
const {	ColorPaletteControl } = gumponents.components;

/**
 * Styles.
 */
import '../../../front-end/components/form-two-step/style.scss';

// Background colors.
export const colors: { [key: string]: string }[] = [
	{ name: __( 'Black', 'qrk' ), color: '#232933', slug: 'black' },
	{ name: __( 'White', 'qrk' ), color: '#FFF', slug: 'white' },
];

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/form-two-step';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Two Step Form', 'qrk' ),
	description: __( 'Display a two step form.', 'qrk' ),
	category: 'forms',
	keywords: [
		__( 'two', 'qrk' ),
		__( 'step', 'qrk' ),
		__( 'form', 'qrk' ),
	],
	attributes: {
		thankYouPageUrl: {
			type: 'string',
		},
		polarRegion: {
			type: 'string',
			default: '',
		},
		ship: {
			type: 'string',
			default: '',
		},
		expedition: {
			type: 'string',
			default: '',
		},
		backgroundColor: {
			type: 'string',
			default: 'black',
			enum: [ 'black', 'white' ],
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
		const blockProps = useBlockProps( {
			className: classnames(
				className,
				'form-two-step',
				'white' === attributes.backgroundColor ? 'form-two-step--background-white' : '',
			),
		} );

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
							] }
							onChange={ ( ship: string ) => setAttributes( { ship } ) }
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
					<Placeholder
						label={ __( 'Two Step Form', 'qrk' ) }
						icon="layout"
					>
						<p>{ __( 'This form will render on the front-end.', 'qrk' ) }</p>
					</Placeholder>
				</div>
			</>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
