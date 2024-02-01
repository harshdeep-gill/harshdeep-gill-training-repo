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
	SelectControl,
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
import '../../../front-end/components/logo-grid/style.scss';
import './editor.scss';

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
export const name: string = 'quark/logo-grid';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Logo Grid', 'qrk' ),
	description: __( 'Display grid of logos.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'logo', 'qrk' ),
		__( 'grid', 'qrk' ),
	],
	attributes: {
		alignment: {
			type: 'string',
			default: 'left',
		},
		size: {
			type: 'string',
			default: 'small',
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
			className: classnames( className, 'logo-grid', 'typography-spacing', `logo-grid--alignment-${ attributes.alignment }`, `logo-grid--${ attributes.size }` ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
			allowedBlocks: [ item.name ],
			template: [ [ item.name ], [ item.name ] ],

			// @ts-ignore
			orientation: 'horizontal',
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Logo Grid Options', 'qrk' ) }>
						<SelectControl
							label={ __( 'Alignment', 'qrk' ) }
							help={ __( 'Select the alignment of the logos.', 'qrk' ) }
							value={ attributes.alignment }
							options={ [
								{ label: __( 'Left', 'qrk' ), value: 'left' },
								{ label: __( 'Right', 'qrk' ), value: 'right' },
								{ label: __( 'Center', 'qrk' ), value: 'center' },
							] }
							onChange={ ( alignment: string ) => setAttributes( { alignment } ) }
						/>
						<SelectControl
							label={ __( 'Size', 'qrk' ) }
							help={ __( 'Select the size of the logos.', 'qrk' ) }
							value={ attributes.size }
							options={ [
								{ label: __( 'Small', 'qrk' ), value: 'small' },
								{ label: __( 'Medium', 'qrk' ), value: 'medium' },
								{ label: __( 'Large', 'qrk' ), value: 'large' },
							] }
							onChange={ ( size: string ) => setAttributes( { size } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps } >
					<div { ...innerBlockProps } />
				</div>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
