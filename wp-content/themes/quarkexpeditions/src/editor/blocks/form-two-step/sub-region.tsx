/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	PanelBody,
	Placeholder,
	TextareaControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * Block name.
 */
export const name: string = 'quark/form-field-sub-region';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Sub Region Field', 'qrk' ),
	description: __( 'Display the Sub Region field', 'qrk' ),
	parent: [ 'quark/form-two-step-landing-form' ],
	category: 'forms',
	keywords: [
		__( 'sub', 'qrk' ),
		__( 'region', 'qrk' ),
		__( 'field', 'qrk' ),
	],
	attributes: {
		label: {
			type: 'string',
			default: '',
		},
		options: {
			type: 'string',
			default: '',
		},
		isRequired: {
			type: 'boolean',
			default: false,
		},
		fieldMap: {
			type: 'string',
			default: 'Sub_Region__c',
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
		const blockProps = useBlockProps( { className } );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Sub Region Field Options', 'qrk' ) }>
						<TextControl
							label={ __( 'Salesforce Field Mapping', 'qrk' ) }
							help={ __( 'Salesforce Field Name', 'qrk' ) }
							value={ attributes.fieldMap }
							onChange={ ( fieldMap: string ) => setAttributes( { fieldMap } ) }
							disabled
						/>
						<TextControl
							label={ __( 'Label', 'qrk' ) }
							help={ __( 'Enter the label for this field.', 'qrk' ) }
							placeholder="Where would you like to travel?"
							value={ attributes.label }
							onChange={ ( label: string ) => setAttributes( { label } ) }
						/>
						<TextareaControl
							label={ __( 'Options', 'qrk' ) }
							help={ __( 'Enter the options for this field with each on a new line in this format - option name :: option value', 'qrk' ) }
							value={ attributes.options }
							onChange={ ( options: string ) => setAttributes( { options } ) }
							rows={ 10 }
							className="form-two-step__textarea"
						/>
						<ToggleControl
							label={ __( 'Is Required?', 'qrk' ) }
							checked={ attributes.isRequired }
							help={ __( 'Is this field required to be filled?', 'qrk' ) }
							onChange={ ( isRequired: boolean ) => setAttributes( { isRequired } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<Placeholder
						label={ __( 'Sub Region Field', 'qrk' ) }
						icon="layout"
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
