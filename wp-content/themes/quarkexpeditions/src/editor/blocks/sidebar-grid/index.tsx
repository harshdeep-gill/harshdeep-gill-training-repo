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
} from '@wordpress/block-editor';

/**
 * Styles.
 */
import '../../../front-end/components/sidebar-grid/style.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child block.
 */
import * as sidebar from './sidebar';
import * as content from './content';

/**
 * Register child block.
 */
registerBlockType( sidebar.name, sidebar.settings );
registerBlockType( content.name, content.settings );

/**
 * Block name.
 */
export const name: string = 'quark/sidebar-grid';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Sidebar Grid', 'qrk' ),
	description: __( 'Add a sidebar grid block.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'sidebar', 'qrk' ),
		__( 'grid', 'qrk' ),
	],
	attributes: {},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'sidebar-grid', 'grid' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
			allowedBlocks: [ content.name, sidebar.name ],
			template: [ [ content.name ], [ sidebar.name ] ],
			templateLock: 'all',
		} );

		// Return the block's markup.
		return (
			<div { ...innerBlockProps } />
		);
	},
	save() {
		// Save inner block content.
		return <InnerBlocks.Content />;
	},
};
