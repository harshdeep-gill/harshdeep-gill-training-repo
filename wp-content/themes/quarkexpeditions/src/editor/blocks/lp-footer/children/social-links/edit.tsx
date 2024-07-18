/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Child block.
 */
import * as socialLinksItem from '../social-links-item';

/**
 * Edit Component.
 */
export default function Edit(): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: 'lp-footer__social-links',
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
		allowedBlocks: [ socialLinksItem.name ],
		template: [
			[ socialLinksItem.name, { type: 'facebook', url: 'https://www.facebook.com/' } ],
			[ socialLinksItem.name, { type: 'instagram', url: 'https://www.instagram.com/' } ],
			[ socialLinksItem.name, { type: 'twitter', url: 'https://www.twitter.com/' } ],
			[ socialLinksItem.name, { type: 'youtube', url: 'https://www.youtube.com/' } ],
		],
	} );

	// Return the block's markup.
	return (
		<div { ...innerBlockProps } />
	);
}
