/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	InnerBlocks,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import * as twoColumns from '../two-columns';
import * as menuList from '../menu-list';
import * as thumbnailCards from '../thumbnail-cards';
import * as thumbnailCard from '../thumbnail-cards/card';

/**
 * Child blocks.
 */
import * as menuItemContentColumn from './menu-item-content-column';
import * as featuredSection from './menu-item-featured-section';

/**
 * Register child block.
 */
registerBlockType( menuItemContentColumn.name, menuItemContentColumn.settings );
registerBlockType( featuredSection.name, featuredSection.settings );

/**
 * Block name.
 */
export const name: string = 'quark/header-menu-item-content';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Header Menu Item Content', 'qrk' ),
	description: __( 'Dropdown Content for Individual Menu Item', 'qrk' ),
	parent: [ 'quark/header-menu-item' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [
		__( 'menu', 'qrk' ),
		__( 'item', 'qrk' ),
		__( 'content', 'qrk' ),
	],
	attributes: {},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ): JSX.Element {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'header__nav-item-dropdown-content' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlocksProps = useInnerBlocksProps( { ...blockProps }, {
			allowedBlocks: [ menuItemContentColumn.name ],
			template: [
				[
					menuItemContentColumn.name,
					{},
					[
						[ featuredSection.name ],
					],
				],
				[
					menuItemContentColumn.name,
					{},
					[
						[
							twoColumns.name,
							{},
							[
								[
									'quark/two-columns-column',
									{},
									[
										[ menuList.name ],
										[
											thumbnailCards.name,
											{ isCarousel: false, isFullWidth: false },
											[
												[ thumbnailCard.name, { orientation: 'landscape' } ],
											],
										],
									],
								],
								[
									'quark/two-columns-column',
									{},
									[
										[ menuList.name ],
										[
											thumbnailCards.name,
											{ isCarousel: false, isFullWidth: false },
											[
												[ thumbnailCard.name, { orientation: 'landscape' } ],
											],
										],
									],
								],
							],
						],
					],
				],
			],
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
