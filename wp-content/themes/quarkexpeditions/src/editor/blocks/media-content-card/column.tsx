/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import {
	InnerBlocks,
	useBlockProps,
	useInnerBlocksProps,
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
 * Child block.
 */
import * as info from './content-info';

/**
 * Register child block.
 */
registerBlockType( info.name, info.settings );

/**
 * Block name.
 */
export const name: string = 'quark/media-content-card-column';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Media Content Card Column', 'qrk' ),
	description: __( 'Individual column for media content card.', 'qrk' ),
	parent: [ 'quark/media-content-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'column', 'qrk' ) ],
	attributes: {
		hasHeading: {
			type: 'boolean',
			default: true,
		},
		heading: {
			type: 'string',
			default: '',
		},
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blocksProps = useBlockProps( {
			className: classnames( className, 'media-content-card__content-column' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlocksProps = useInnerBlocksProps( {}, {
			allowedBlocks: [ 'core/paragraph', info.name ],
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Media Content Card Column Options', 'qrk' ) }>
						<ToggleControl
							label={ __( 'Has Heading?', 'qrk' ) }
							checked={ attributes.hasHeading }
							help={ __( 'Does this column have a heading?', 'qrk' ) }
							onChange={ ( hasHeading: boolean ) => setAttributes( { hasHeading } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blocksProps }>
					{ attributes.hasHeading &&
						<RichText
							tagName="p"
							className="h4"
							placeholder={ __( 'Write Heading… ', 'qrk' ) }
							value={ attributes.heading }
							onChange={ ( heading: string ) => setAttributes( { heading } ) }
							allowedFormats={ [] }
						/>
					}
					<div { ...innerBlocksProps } />
				</div>
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
