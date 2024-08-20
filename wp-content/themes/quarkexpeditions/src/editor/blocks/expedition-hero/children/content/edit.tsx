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
import * as expeditionHeroContentLeft from '../content-left';
import * as expeditionHeroContentRight from '../content-right';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'expedition-hero__content', 'two-columns', 'grid' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			allowedBlocks: [ expeditionHeroContentLeft.name, expeditionHeroContentRight.name ],
			template: [ [ expeditionHeroContentLeft.name ], [ expeditionHeroContentRight.name ] ],
		}
	);

	// Return the block's markup.
	return <div { ...innerBlockProps } />;
}
