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
import { PanelBody, TextControl } from '@wordpress/components';

/**
 * Child blocks.
 */
import * as socialLinks from './social-links';
import * as icon from './icon';

/**
 * Register child blocks.
 */
registerBlockType( socialLinks.name, socialLinks.settings );
registerBlockType( icon.name, icon.settings );

/**
 * Block name.
 */
export const name: string = 'quark/lp-footer-column';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Footer Column', 'qrk' ),
	description: __( 'Footer column block.', 'qrk' ),
	parent: [ 'quark/lp-footer-row' ],
	icon: 'columns',
	category: 'layout',
	keywords: [ __( 'column', 'qrk' ) ],
	attributes: {
		url: {
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
	edit( { attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( { className: 'lp-footer__column' } );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {}, {
			allowedBlocks: [ 'core/paragraph', 'core/list', socialLinks.name, 'core/heading', 'quark/logo-grid' ],
			template: [
				[ 'core/paragraph' ],
			],
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Column Options', 'qrk' ) }>
						<TextControl
							label="Enter URL for the block"
							value={ attributes.url }
							onChange={ ( url ) => setAttributes( { url } ) }
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
