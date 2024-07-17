/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
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
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( { className } );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Season Field Options', 'qrk' ) }>
					<TextControl
						label={ __( 'Salesforce Field Mapping', 'qrk' ) }
						help={ __( 'Salesforce Field Name', 'qrk' ) }
						value="Season__c"
						disabled
						onChange={ () => {
							// Placeholder function.
						} }
					/>
					<TextControl
						label={ __( 'Label', 'qrk' ) }
						help={ __( 'Enter the label for this field.', 'qrk' ) }
						placeholder="When would you like to go?"
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
					label={ __( 'Season Field', 'qrk' ) }
					icon="layout"
				/>
			</div>
		</>
	);
}
