/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	RichText,
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/lp-form-modal-cta';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Landing Page - Form modal CTA', 'qrk' ),
	description: __( 'A CTA to open up a Landing Page Form Modal.', 'qrk' ),
	icon: 'button',
	category: 'widgets',
	keywords: [ __( 'landing', 'qrk' ), __( 'cta', 'qrk' ), __( 'form', 'qrk' ), __( 'modal', 'qrk' ) ],
	attributes: {
		text: {
			type: 'string',
			default: '',
		},
		fields: {
			type: 'object',
			default: {},
		}
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: true,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( { className: classnames( className, 'lp-form-modal-cta' ) } );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Form Modal CTA Hidden Fields', 'qrk' ) }>
						<TextControl
							label={ __( 'Polar Region', 'qrk' ) }
							help={ __( 'Enter the value for Polar Region.', 'qrk' ) }
							value={ attributes.fields.polarRegion }
							onChange={ ( polarRegion: string ) => setAttributes( { fields: { ...attributes.fields,polarRegion } } ) }
						/>
						<TextControl
							label={ __( 'Season', 'qrk' ) }
							help={ __( 'Enter the value for Season.', 'qrk' ) }
							value={ attributes.fields.season }
							onChange={ ( season: string ) => setAttributes( { fields: { ...attributes.fields, season } } ) }
						/>
						<TextControl
							label={ __( 'Ship', 'qrk' ) }
							help={ __( 'Enter the value for Ship.', 'qrk' ) }
							value={ attributes.fields.ship }
							onChange={ ( ship: string ) => setAttributes( { fields: { ...attributes.fields, ship } } ) }
						/>
						<TextControl
							label={ __( 'Sub Region', 'qrk' ) }
							help={ __( 'Enter the value for Sub Region.', 'qrk' ) }
							value={ attributes.fields.subRegion }
							onChange={ ( subRegion: string ) => setAttributes( { fields: { ...attributes.fields, subRegion } } ) }
						/>
						<TextControl
							label={ __( 'Expedition', 'qrk' ) }
							help={ __( 'Enter the value for Expedition.', 'qrk' ) }
							value={ attributes.fields.expedition }
							onChange={ ( expedition: string ) => setAttributes( { fields: { ...attributes.fields, expedition } } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<RichText
						tagName="span"
						className={ classnames( 'btn', 'btn--size-big' ) }
						placeholder={ __( 'Write CTA text…', 'qrk' ) }
						value={ attributes.text }
						onChange={ ( text: string ) => setAttributes( { text } ) }
						allowedFormats={ [] }
					/>
				</div>
			</>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
