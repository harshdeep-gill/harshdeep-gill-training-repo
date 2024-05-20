/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
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
export const name: string = 'quark/sidebar-grid-content';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Content', 'qrk' ),
	description: __( 'Content within Sidebar Grid Block.', 'qrk' ),
	parent: [ 'quark/sidebar-grid' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'content', 'qrk' ) ],
	attributes: {},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ) : JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blocksProps = useBlockProps( {
			className: classnames( className, 'sidebar-grid__content' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlocksProps = useInnerBlocksProps( { ...blocksProps }, {
			template: [ [ 'core/paragraph', { placeholder: __( 'Content…', 'qrk' ) } ] ],
			templateLock: false,
		} );

		// Return the block's markup.
		return (
			<div { ...innerBlocksProps } />
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
