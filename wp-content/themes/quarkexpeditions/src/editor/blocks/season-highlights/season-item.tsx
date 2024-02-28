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
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child blocks.
 */
import * as highlight from './highlight';

/**
 * Register child blocks.
 */
registerBlockType( highlight.name, highlight.settings );

/**
 * Block name.
 */
export const name: string = 'quark/season-highlights-season-item';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Season Item', 'qrk' ),
	description: __( 'Item section within a Season', 'qrk' ),
	parent: [ 'quark/season-highlights-season' ],
	icon: 'layout',
	category: 'layout',
	keywords: [ __( 'item', 'qrk' ) ],
	attributes: {
		title: {
			type: 'string',
			default: '',
		},
		hasLightBackground: {
			type: 'boolean',
			default: false,
		}
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames(
				className,
				'season-highlights__item',
				true === attributes.hasLightBackground ? 'season-highlights__item--light' : ''
			),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( { ...blockProps}, {
			allowedBlocks: [ highlight.name ],
			template: [
				[ highlight.name ],
			],
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Season Item Options', 'qrk' ) }>
						<ToggleControl
							label={ __( 'Has Light Background?', 'qrk' ) }
							checked={ attributes.hasLightBackground }
							onChange={ () => setAttributes( { hasLightBackground: ! attributes.hasLightBackground } ) }
							help={ __( 'Do the highlights within this item have a light background?', 'qrk' ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps } >
					<RichText
						tagName="p"
						className="season-highlights__item-title"
						placeholder={ __( 'Write Season Item Title…', 'qrk' ) }
						value={ attributes.title }
						onChange={ ( title: string ) => setAttributes( { title } ) }
						allowedFormats={ [] }
					/>
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
