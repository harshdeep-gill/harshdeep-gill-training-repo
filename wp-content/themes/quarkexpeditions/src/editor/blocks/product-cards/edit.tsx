/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

/**
 * Internal dependencies.
 */
import card from './children/card/block.json';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Styles.
 */
import './editor.scss';

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
	const blockProps = useBlockProps();

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps(
		{
			className: classnames( className, 'product-cards', 'grid', 'center' === attributes.align ? 'product-cards--align-center' : 'grid--cols-3' ),
		},
		{
			allowedBlocks: [ card.name ],
			template: [ [ card.name ], [ card.name ], [ card.name ] ],
			renderAppender: InnerBlocks.ButtonBlockAppender,

			// @ts-ignore
			orientation: 'horizontal',
		},
	);

	// Return the block's markup.
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Product Cards Grid Options', 'qrk' ) }>
					<SelectControl
						label={ __( 'Product Cards Alignment', 'qrk' ) }
						help={ __( 'Select the cards alignment', 'qrk' ) }
						value={ attributes.align }
						options={ [
							{ label: __( 'Left', 'qrk' ), value: 'left' },
							{ label: __( 'Center', 'qrk' ), value: 'center' },
						] }
						onChange={ ( align: string ) => setAttributes( { align } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<div { ...innerBlockProps } />
			</div>
		</>
	);
}
