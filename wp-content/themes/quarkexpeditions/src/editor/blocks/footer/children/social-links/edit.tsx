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
import * as socialLink from '../social-link';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'footer__social-icons' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			allowedBlocks: [ socialLink.name ],
			template: [
				[ socialLink.name, { type: 'facebook' } ],
				[ socialLink.name, { type: 'instagram' } ],
				[ socialLink.name, { type: 'twitter' } ],
				[ socialLink.name, { type: 'youtube' } ],
			],

			// @ts-ignore
			orientation: 'horizontal',
		}
	);

	// Return the block's markup.
	return ( <ul { ...innerBlockProps } /> );
}
