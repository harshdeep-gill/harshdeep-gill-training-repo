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
 * Styles.
 */
import '../../../front-end/components/inquiry-form/style.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/inquiry-form';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Inquiry Form', 'qrk' ),
	description: __( 'Display an inquiry form.', 'qrk' ),
	category: 'forms',
	keywords: [
		__( 'inquiry', 'qrk' ),
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
		subRegion: {
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
			className: classnames( className, 'inquiry-form' ),
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Inquiry Form Options', 'qrk' ) }>
						<SelectControl
							label={ __( 'Form Type', 'qrk' ) }
							help={ __( 'Select the form to display here.', 'qrk' ) }
							value={ attributes.formType }
							options={ [
								{ label: __( 'Inquiry Form', 'qrk' ), value: 'inquiry-form' },
								{ label: __( 'Inquiry Form Compact', 'qrk' ), value: 'inquiry-form-compact' },
							] }
							onChange={ ( formType: string ) => setAttributes( { formType } ) }
						/>
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
					</PanelBody>
					<PanelBody title={ __( 'Inquiry Form Hidden Fields', 'qrk' ) }>
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
							label={ __( 'Sub Region', 'qrk' ) }
							help={ __( 'Select the value for Sub Region, only if the field is not present in the Inquiry Form.', 'qrk' ) }
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
							] }
							onChange={ ( subRegion: string ) => setAttributes( { subRegion } ) }
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
						label={ __( 'Inquiry Form', 'qrk' ) }
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
