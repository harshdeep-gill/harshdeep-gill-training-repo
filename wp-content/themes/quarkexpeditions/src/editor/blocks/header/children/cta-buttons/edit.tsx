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
import * as raqButton from '../raq-button';
import * as contactButton from '../contact-button';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'header__cta-buttons', 'color-context--dark' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps },
		{
			allowedBlocks: [ raqButton.name, contactButton.name ],
			template: [
				[ contactButton.name, { isSizeBig: true, backgroundColor: 'black', appearance: 'outline' } ],
				[ raqButton.name, { isSizeBig: true } ],
			],
		},
	);

	// Return the block's markup.
	return (
		<div { ...innerBlockProps } />
	);
}
