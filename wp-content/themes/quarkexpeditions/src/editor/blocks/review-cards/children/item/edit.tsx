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
 * Child blocks.
 */
import * as title from '../title';
import * as review from '../review';
import * as rating from '../rating';
import * as author from '../author';
import * as authorDetails from '../author-details';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ) : JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'review-cards__card' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps },
		{
			allowedBlocks: [ rating.name, title.name, review.name, author.name, authorDetails.name ],
			template: [
				[ rating.name ],
				[ title.name ],
				[ review.name ],
				[ author.name ],
				[ authorDetails.name ],
			],
			templateLock: 'insert',
		},
	);

	// Return the block's markup.
	return (
		<div { ...innerBlockProps } />
	);
}
