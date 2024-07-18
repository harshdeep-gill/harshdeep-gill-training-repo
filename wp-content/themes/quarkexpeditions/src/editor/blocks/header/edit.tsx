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
import icons from '../icons';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Styles.
 */
import './editor.scss';

/**
 * Child blocks.
 */
import * as megaMenu from './children/mega-menu';
import * as secondaryNav from './children/secondary-nav';
import * as ctaButtons from './children/cta-buttons';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ) {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'header', 'full-width' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps },
		{
			allowedBlocks: [ megaMenu.name, secondaryNav.name, ctaButtons.name ],
			template: [ [ megaMenu.name ], [ secondaryNav.name ], [ ctaButtons.name ] ],
			orientation: 'horizontal',
		}
	);

	// Return block.
	return (
		<header { ...blockProps }>
			<a href="/" className="header__logo">
				{ icons.logo }
			</a>
			<nav { ...innerBlockProps } />
		</header>
	);
}
