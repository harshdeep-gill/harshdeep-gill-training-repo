/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
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
 * Child blocks.
 */
import * as menuItem from './menu-item';

/**
 * Register child blocks.
 */
registerBlockType( menuItem.name, menuItem.settings );

/**
 * Block name.
 */
export const name: string = 'quark/header-mega-menu';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Header Mega Menu', 'qrk' ),
	description: __( 'Add one or more mega menu items.', 'qrk' ),
	parent: [ 'quark/header' ],
	category: 'layout',
	keywords: [
		__( 'header', 'qrk' ),
	],
	attributes: {},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ) {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'header__primary-nav' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( {
			className: classnames( 'header__nav-menu' ),
		},
		{
			allowedBlocks: [ menuItem.name ],
			template: [
				[ menuItem.name ],
				[ menuItem.name ],
				[ menuItem.name ],
				[ menuItem.name ],
				[ menuItem.name ],
			],
			renderAppender: InnerBlocks.DefaultBlockAppender,

			// @ts-ignore
			orientation: 'horizontal',
		}
		);

		// Return block.
		return (
			<nav { ...blockProps } >
				<ul { ...innerBlockProps } />
			</nav>
		);
	},
	save() {
		// Save InnerBlock Content.
		return <InnerBlocks.Content />;
	},
};
