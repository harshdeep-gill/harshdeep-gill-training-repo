/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InspectorControls,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child block.
 */
import * as item from './children/item';
import { PanelBody, ToggleControl } from '@wordpress/components';

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
		className: classnames( className, 'accordion', {
			'accordion--full-border': attributes.hasBorder,
		} ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
		allowedBlocks: [ item.name ],
		template: [ [ item.name ], [ item.name ] ],

		// @ts-ignore
		orientation: 'vertical',
	} );

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Accordion Options.', 'qrk' ) }>
					<ToggleControl
						label={ __( 'Has Borders?', 'qrk' ) }
						checked={ attributes.hasBorder }
						help={ __( 'Should all accordion items have borders?', 'qrk' ) }
						onChange={ ( hasBorder: boolean ) => setAttributes( { hasBorder } ) }
					/>
					<ToggleControl
						label={ __( 'FAQ Schema', 'qrk' ) }
						checked={ attributes.faqSchema }
						onChange={ ( faqSchema ) => setAttributes( { faqSchema } ) }
						help={ __( 'Should an SEO FAQ schema be added for this accordion?', 'qrk' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...innerBlockProps } />
		</>
	);
}
