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
import * as footerColumn from '../column';
import * as footerNavigation from '../navigation';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'footer__middle' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			allowedBlocks: [ footerColumn.name, footerNavigation.name ],
			template: [
				[ footerColumn.name ],
				[ footerNavigation.name ],
				[ footerNavigation.name ],
				[ footerColumn.name ],
				[ footerNavigation.name ],
				[ footerNavigation.name ],
				[ footerColumn.name ],
			],

			// @ts-ignore
			orientation: 'horizontal',
		}
	);

	// Return the block's markup.
	return ( <div { ...innerBlockProps } /> );
}
