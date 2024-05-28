/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	PanelBody,
	ToggleControl,
} from '@wordpress/components';
import {
	InspectorControls,
	InnerBlocks,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/sidebar-grid-sidebar';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Sidebar', 'qrk' ),
	description: __( 'Sidebar within Sidebar Grid Block.', 'qrk' ),
	parent: [ 'quark/sidebar-grid' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'sidebar', 'qrk' ) ],
	attributes: {
		stickySidebar: {
			type: 'boolean',
			default: true,
		},
		showOnMobile: {
			type: 'boolean',
			default: false,
		},
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ) : JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blocksProps = useBlockProps( {
			className: classnames( className, 'sidebar-grid__sidebar', {
				'sidebar-grid__sidebar--sticky': attributes.stickySidebar,
				'sidebar-grid__sidebar--show-on-mobile': attributes.showOnMobile,
			} ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlocksProps = useInnerBlocksProps( { ...blocksProps }, {
			template: [ [ 'core/paragraph', { placeholder: __( 'Sidebar…', 'qrk' ) } ] ],
			templateLock: false,
		} );

		// Return the block's markup.
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Sidebar Grid Options', 'qrk' ) }>
						<ToggleControl
							label={ __( 'Sticky Sidebar', 'qrk' ) }
							checked={ attributes.stickySidebar }
							help={ __( 'Should the sidebar be sticky on scroll?', 'qrk' ) }
							onChange={ ( stickySidebar: boolean ) => setAttributes( { stickySidebar } ) }
						/>
						<ToggleControl
							label={ __( 'Show on Mobile', 'qrk' ) }
							checked={ attributes.showOnMobile }
							help={ __( 'Should the sidebar be visible on mobile devices?', 'qrk' ) }
							onChange={ ( showOnMobile: boolean ) => setAttributes( { showOnMobile } ) }
						/>
					</PanelBody>
				</InspectorControls>
				<aside { ...innerBlocksProps } />
			</>
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
