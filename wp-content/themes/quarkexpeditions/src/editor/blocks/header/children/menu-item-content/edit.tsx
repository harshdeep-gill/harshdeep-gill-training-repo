/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import * as twoColumns from '../../../two-columns';
import * as menuList from '../../../menu-list';
import * as thumbnailCards from '../../../thumbnail-cards';
import * as thumbnailCard from '../../../thumbnail-cards/children/card';

/**
 * Child blocks.
 */
import * as menuItemContentColumn from '../menu-item-content-column';
import * as featuredSection from '../menu-item-featured-section';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
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
}
