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
 * Children blocks
 */
import * as footerTop from './children/top';
import * as footerMiddle from './children/middle';
import * as footerBottom from './children/bottom';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'footer',
			'full-width',
			'color-context--dark'
		),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			allowedBlocks: [ footerTop.name, footerMiddle.name, footerBottom.name ],
			template: [ [ footerTop.name ], [ footerMiddle.name ], [ footerBottom.name ] ],

			// @ts-ignore
			orientation: 'vertical',
		}
	);

	// Return the block's markup.
	return (
		<footer { ...innerBlockProps } />
	);
}
