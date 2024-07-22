/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
} from '@wordpress/components';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child block.
 */
import * as item from './children/icon-columns-column';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Initialize the class for the variant
	let variantClass = '';

	// Select the appropriate class
	if ( 'dark' === attributes.variant ) {
		variantClass = 'color-context--dark';
	} else if ( 'light' === attributes.variant ) {
		variantClass = 'icon-columns--light';
	}

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'icon-columns', variantClass, ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
		allowedBlocks: [ item.name ],
		template: [ [ item.name ], [ item.name ], [ item.name ], [ item.name ], [ item.name ] ],

		// @ts-ignore
		orientation: 'horizontal',
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Columns Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Variant', 'qrk' ) }
						help={ __( 'Select the variant.', 'qrk' ) }
						value={ attributes.variant }
						options={ [
							{ label: __( 'Duotone', 'qrk' ), value: '' },
							{ label: __( 'Dark', 'qrk' ), value: 'dark' },
							{ label: __( 'Light', 'qrk' ), value: 'light' },
						] }
						onChange={ ( variant: string ) => setAttributes( { variant } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Section { ...blockProps } >
				<div { ...innerBlockProps } />
			</Section>
		</>
	);
}
