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
 * Child Blocks.
 */
import * as title from '../title';
import * as overline from '../overline';
import * as subtitle from '../subtitle';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Set block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'search-hero__title-container' ),
	} );

	// Set inner block props.
	const innerBlockProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			allowedBlocks: [ overline.name, title.name, subtitle.name ],
			template: [ [ overline.name ], [ title.name ], [ subtitle.name ] ],
		}
	);

	// Return the block's markup.
	return <div { ...innerBlockProps } />;
}
