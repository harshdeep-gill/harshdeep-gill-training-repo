/**
 * WordPress dependencies.
 */
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies.
 */
import classnames from 'classnames';
import { PanelBody, SelectControl } from '@wordpress/components';

/**
 * Edit component.
 *
 * @param {Object} props               Component properties.
 * @param {Object} props.className     Class name.
 * @param {Object} props.attributes    Block attributes.
 * @param {Object} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blocksProps = useBlockProps( {
		className: classnames( className, 'media-text-cta__title' ),
	} );

	// Determine the tag based on the heading level.
	const tag = '2' === attributes.headingLevel ? 'h2' : '3' === attributes.headingLevel ? 'h3' : 'h4'; // eslint-disable-line no-nested-ternary

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Content Title Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Heading Level', 'qrk' ) }
						value={ attributes.headingLevel }
						options={ [
							{ label: '2', value: '2' },
							{ label: '3', value: '3' },
							{ label: '4', value: '4' },
						] }
						onChange={ ( headingLevel: string ) => setAttributes( { headingLevel } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<RichText
				{ ...blocksProps }
				tagName={ tag }
				placeholder={ __( 'Write Content Title… ', 'qrk' ) }
				value={ attributes.title }
				onChange={ ( title: string ) => setAttributes( { title } ) }
				allowedFormats={ [] }
			/>
		</>
	);
}
