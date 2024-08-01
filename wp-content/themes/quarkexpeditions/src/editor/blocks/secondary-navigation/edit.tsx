/**
 * WordPress dependencies.
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classNames from 'classnames';

/**
 * Styles.
 */
import './editor.scss';

/**
 * Child blocks.
 */
import * as secondaryNavigationMenu from './children/menu';
import * as secondaryNavigationButtons from './children/buttons';

/**
 * Edit Component.
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ) {
	// Get block props.
	const blockProps = useBlockProps( {
		className: classNames( className, 'secondary-navigation' ),
	} );

	// Get inner blocks props.
	const innerBlockProps = useInnerBlocksProps(
		{ className: 'secondary-navigation__wrap wrap' },
		{
			allowedBlocks: [ secondaryNavigationMenu.name, secondaryNavigationButtons.name ],
			template: [ [ secondaryNavigationMenu.name ], [ secondaryNavigationButtons.name ] ],
			orientation: 'horizontal',
		}
	);

	// Return markup.
	return (
		<section { ...blockProps }>
			<div { ...innerBlockProps } />
		</section>
	);
}
