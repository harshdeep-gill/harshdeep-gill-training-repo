/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	InspectorControls,
	InnerBlocks,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
} from '@wordpress/components';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { ImageControl, Img } = gumponents.components;

/**
 * Styles.
 */
import '../../../front-end/components/media-content-card/style.scss';
import './editor.scss';

/**
 * Child block.
 */
import * as column from './column';
import * as info from './content-info';

/**
 * Register child block.
 */
registerBlockType( column.name, column.settings );
registerBlockType( info.name, info.settings );

/**
 * Block name.
 */
export const name: string = 'quark/media-content-card';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Media Content Card', 'qrk' ),
	description: __( 'Add a media content card into a grid', 'qrk' ),
	category: 'layout',
	keywords: [ __( 'media content card', 'qrk' ) ],
	attributes: {
		isCompact: {
			type: 'boolean',
			default: false,
		},
		image: {
			type: 'object',
			default: {},
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
			className: classnames(
				className,
				'media-content-card',
				attributes.isCompact ? 'media-content-card--compact' : '',
			),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {
			className: classnames( 'media-content-card__content' ),
		},
		{
			allowedBlocks: [ column.name ],
			template: [
				[
					column.name,
					{},
					[
						[ 'core/paragraph', { placeholder: __( 'Write content…', 'qrk' ) } ],
					],
				],
				[
					column.name,
					{},
					[
						[ info.name ],
						[ info.name ],
					],
				],
			],

			// @ts-ignore.
			orientation: 'horizontal',
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Media Content Card Options', 'qrk' ) }>
						<ToggleControl
							label={ __( 'Compact Layout', 'qrk' ) }
							checked={ attributes.isCompact }
							help={ __( 'Change to a single column layout.', 'qrk' ) }
							onChange={ ( isCompact: boolean ) => setAttributes( { isCompact } ) }
						/>
						<ImageControl
							label={ __( 'Image', 'qrk' ) }
							value={ attributes.image ? attributes.image.id : null }
							size="full"
							help={ __( 'Choose an image.', 'qrk' ) }
							onChange={ ( image: object ) => setAttributes( { image } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps } >
					<figure className="media-content-card__image">
						<Img
							value={ attributes.image }
						/>
					</figure>
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
