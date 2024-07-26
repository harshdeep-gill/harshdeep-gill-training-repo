/**
 * WordPress dependencies.
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classNames from 'classnames';

/**
 * Child blocks.
 */
import * as secondaryNavigationMenu from './children/secondary-navigation-menu';

/**
 * Edit Component.
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 * @return
 */
export default function Edit( { className }: BlockEditAttributes ) {
    // eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classNames( className, 'secondary-navigation__wrap' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			allowedBlocks: [ secondaryNavigationMenu.name ],
			template: [ [ secondaryNavigationMenu.name ] ],
			orientation: 'horizontal',
		}
	);

	// Return markup.
	return (
		<div { ...innerBlockProps } />
	);
}
