/**
 * WordPress dependencies
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Child blocks.
 */
import * as contactInfoItem from '../contact-info-item';

/**
 * Edit component.
 *
 * @param {Object} props           Component properties.
 * @param {Object} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'contact-cover-card__contact-info', 'body-small' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps },
		{
			allowedBlocks: [ contactInfoItem.name ],
			template: [ [ contactInfoItem.name ], [ contactInfoItem.name ] ],
		} );

	// Return the block's markup.
	return (
		<div { ...innerBlockProps } />
	);
}
