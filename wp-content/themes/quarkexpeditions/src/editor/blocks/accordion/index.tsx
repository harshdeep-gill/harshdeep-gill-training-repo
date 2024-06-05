/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	BlockConfiguration,
	registerBlockType,
} from '@wordpress/blocks';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';

/**
 * Styles.
 */
import '../../../front-end/components/accordion/style.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child block.
 */
import * as item from './item';
import { PanelBody, ToggleControl } from '@wordpress/components';

/**
 * Register child block.
 */
registerBlockType( item.name, item.settings );

/**
 * Block name.
 */
export const name: string = 'quark/accordion';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Accordion', 'qrk' ),
	description: __( 'Add Accordion Block with one or more items', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'accordion', 'qrk' ),
	],
	attributes: {
		hasBorder: {
			type: 'boolean',
			default: true,
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
					</PanelBody>
				</InspectorControls>
				<div { ...innerBlockProps } />
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
