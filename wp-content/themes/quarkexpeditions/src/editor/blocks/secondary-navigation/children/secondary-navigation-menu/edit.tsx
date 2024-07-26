/**
 * WordPress dependencies.
 */
import { InnerBlocks, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child blocks.
 */
import * as secondaryNavigationItem from '../secondary-navigation-item';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ) {
	const blockProps = useBlockProps( {
		className: classnames( className, 'secondary-navigation__navigation' ),
	} );

	// TODO: Add comment.
	const innerBlockProps = useInnerBlocksProps(
		{
			className: classnames( 'secondary-navigation__navigation-items' ),
		},
		{
			allowedBlocks: [ secondaryNavigationItem.name ],
			template: [
				[ secondaryNavigationItem.name, { placeholder: 'Secondary Navigation Item…' } ],
				[ secondaryNavigationItem.name, { placeholder: 'Secondary Navigation Item…' } ],
				[ secondaryNavigationItem.name, { placeholder: 'Secondary Navigation Item…' } ],
			],
			renderAppender: InnerBlocks.DefaultBlockAppender,
		}
	);

	// TODO: Add comment.
	return (
		<nav { ...blockProps } >
			<ul { ...innerBlockProps } />
		</nav>
	);
}
