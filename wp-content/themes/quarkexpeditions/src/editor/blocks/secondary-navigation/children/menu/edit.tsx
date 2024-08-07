/**
 * WordPress dependencies.
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child blocks.
 */
import * as secondaryNavigationItem from '../item';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ) {
	// Get block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'secondary-navigation__navigation' ),
	} );

	// Get inner blocks props.
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
		}
	);

	// Return markup.
	return (
		<nav { ...blockProps } >
			<ul { ...innerBlockProps } />
		</nav>
	);
}
