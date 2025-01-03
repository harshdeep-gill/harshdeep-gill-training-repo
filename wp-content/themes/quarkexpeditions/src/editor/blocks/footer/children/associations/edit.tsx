/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import * as associationLink from '../association-link';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( { className: classnames( className, 'footer__associations' ) } );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			allowedBlocks: [ associationLink.name ],
			template: [
				[ associationLink.name, { type: 'iaato' } ],
				[ associationLink.name, { type: 'aeco' } ],
			],

			// @ts-ignore
			orientation: 'horizontal',
		}
	);

	// Return the block's markup.
	return (
		<ul { ...innerBlockProps } />
	);
}
