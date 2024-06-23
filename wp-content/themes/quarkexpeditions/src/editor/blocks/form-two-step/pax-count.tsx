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
export const name: string = 'quark/form-field-pax-count';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Pax Count Field', 'qrk' ),
	description: __( 'Display the Pax Count Field', 'qrk' ),
	parent: [ 'quark/form-two-step-landing-form' ],
	category: 'forms',
	keywords: [
		__( 'pax', 'qrk' ),
		__( 'count', 'qrk' ),
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
			default: 'Pax_Count__c',
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
					<PanelBody title={ __( 'Pax Count Field Options', 'qrk' ) }>
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
							placeholder="How many guests?"
							value={ attributes.label }
							onChange={ ( label: string ) => setAttributes( { label } ) }
						/>
						<TextareaControl
							label={ __( 'Options', 'qrk' ) }
							help={ __( 'Enter the options for this field with each on a new line in this format - option name :: option value', 'qrk' ) }
							value={ attributes.options }
							onChange={ ( options: string ) => setAttributes( { options } ) }
							rows={ 10 }
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
						label={ __( 'Pax Count Field', 'qrk' ) }
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
