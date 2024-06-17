/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	BlockConfiguration,
	registerBlockType,
} from '@wordpress/blocks';
import {
	PanelBody,
	ToggleControl,
} from '@wordpress/components';
import {
	InspectorControls,
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
} from '@wordpress/block-editor';

/**
 * Styles.
 */
import '../../../front-end/components/two-columns/style.scss';
import './editor.scss';

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
import * as item from './item';

/**
 * Register child block.
 */
registerBlockType( item.name, item.settings );

/**
 * Block name.
 */
export const name: string = 'quark/two-columns';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Two Columns', 'qrk' ),
	description: __( 'Add a two column block.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'two', 'qrk' ),
		__( 'columns', 'qrk' ),
	],
	attributes: {
		hasBorder: {
			type: 'boolean',
			default: true,
		},
		hasOnlyChild: {
			type: 'boolean',
			default: false,
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
			className: classnames( className ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {
			className: classnames( 'two-columns', {
				'grid grid--cols-2': ! attributes.hasOnlyChild,
				'two-columns--has-border': attributes.hasBorder,
				'two-columns--only-child': attributes.hasOnlyChild,
			} ),
		}, {
			allowedBlocks: [ item.name ],
			template: [ [ item.name ], [ item.name ] ],
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Two Columns Options', 'qrk' ) }>
						<ToggleControl
							label={ __( 'Has Borders', 'qrk' ) }
							checked={ attributes.hasBorder }
							help={ __( 'Should these columns have borders?', 'qrk' ) }
							onChange={ ( hasBorder: boolean ) => setAttributes( { hasBorder } ) }
						/>
						<ToggleControl
							label={ __( 'Has One Column?', 'qrk' ) }
							checked={ attributes.hasOnlyChild }
							help={ __( 'Does this column have only one column?', 'qrk' ) }
							onChange={ ( hasOnlyChild: boolean ) => setAttributes( { hasOnlyChild, hasBorder: false } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<Section { ...blockProps }>
					<div { ...innerBlockProps } />
				</Section>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
