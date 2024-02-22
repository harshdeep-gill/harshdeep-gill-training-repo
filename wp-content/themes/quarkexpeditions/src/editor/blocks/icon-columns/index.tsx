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
import {
	PanelBody,
	SelectControl,
} from '@wordpress/components';

/**
 * Styles.
 */
import '../../../front-end/components/icon-columns/style.scss';

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
export const name: string = 'quark/icon-columns';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Icon Columns', 'qrk' ),
	description: __( 'Display a grid of icons with title.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'icon', 'qrk' ),
		__( 'columns', 'qrk' ),
	],
	attributes: {
		variant: {
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
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
