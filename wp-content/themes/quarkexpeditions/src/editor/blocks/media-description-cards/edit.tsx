/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
} from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';
import icons from '../icons';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Styles.
 */
import './editor.scss';

/**
 * Children blocks
 */
import * as card from './children/card';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// Set block props.
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'media-description-cards',
		),
	} );

	// Set inner blocks props.
	const innerBlockProps = useInnerBlocksProps(
		{ className: 'media-description-cards__slides-container' },
		{
			allowedBlocks: [ card.name ],
			template: [ [ card.name ], [ card.name ], [ card.name ] ],
			renderAppender: InnerBlocks.ButtonBlockAppender,

			// @ts-ignore
			orientation: 'horizontal',
		}
	);

	// Return the block's markup.
	return (
		<Section { ...blockProps } >
			<div className="media-description-cards">
				<div { ...innerBlockProps } />
			</div>
			<div className="media-description-cards__nav">
				<div className="media-description-cards__arrow-button media-description-cards__arrow-button--left">
					{ icons.chevronLeft }
				</div>
				<div className="media-description-cards__arrow-button media-description-cards__arrow-button--right">
					{ icons.chevronLeft }
				</div>
			</div>
		</Section>
	);
}
