/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Styles.
 */
import './editor.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child block - LP Footer row.
 */
import * as item from './children/lp-footer-item';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'lp-footer', 'full-width', 'section', 'section--seamless' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {
		className: 'lp-footer__wrap',
	}, {
		allowedBlocks: [ item.name ],
		template: [
			[ item.name ],
		],

		// @ts-ignore
		orientation: 'horizontal',
	} );

	// Return the block's markup.
	return (
		<footer { ...blockProps } >
			<div { ...innerBlockProps } />
		</footer>
	);
}
