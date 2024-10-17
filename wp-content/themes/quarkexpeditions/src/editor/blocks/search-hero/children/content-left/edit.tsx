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
import * as thumbnailCards from '../../../thumbnail-cards';

/**
 * Child Blocks.
 */
import * as titleContainer from '../title-container';
import * as searchBar from '../search-bar';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Set block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'search-hero__left' ),
	} );

	// Set inner block props.
	const innerBlockProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			allowedBlocks: [ titleContainer.name, searchBar.name, thumbnailCards.name ],
			template: [ [ titleContainer.name ], [ searchBar.name ], [ thumbnailCards.name, { isCarousel: false } ] ],
		}
	);

	// Return the block's markup.
	return <div { ...innerBlockProps } />;
}
