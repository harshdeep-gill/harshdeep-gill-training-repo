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
 * Children blocks
 */
import * as footerNavigation from '../navigation';
import * as footerCopyrightText from '../copyright';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'footer__bottom' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			allowedBlocks: [ footerNavigation.name, footerCopyrightText.name ],
			template: [ [ footerCopyrightText.name ], [ footerNavigation.name ] ],

			// @ts-ignore
			orientation: 'horizontal',
		}
	);

	// Return the block's markup.
	return ( <div { ...innerBlockProps } /> );
}
