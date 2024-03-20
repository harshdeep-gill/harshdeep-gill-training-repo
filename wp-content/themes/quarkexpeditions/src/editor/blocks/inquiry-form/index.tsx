/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	PanelBody,
	Placeholder,
	BaseControl,
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
	category: 'layout',
	keywords: [
		__( 'inquiry', 'qrk' ),
		__( 'form', 'qrk' ),
	],
	attributes: {
		thankYouPageUrl: {
			type: 'string',
		},
		formType: {
			type: 'string',
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
