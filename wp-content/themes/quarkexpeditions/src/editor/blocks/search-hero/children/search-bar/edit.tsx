/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import * as searchFiltersBar from '../../../search-filters-bar';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Set block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'search-hero__search-bar' ),
	} );

	// Set inner block props - Only Allow Hero Card Slider block.
	const innerBlockProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			allowedBlocks: [ searchFiltersBar.name ],
			template: [ [ searchFiltersBar.name ] ],
		}
	);

	// Return the block's markup.
	return <div { ...innerBlockProps } />;
}
