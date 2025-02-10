/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import * as formModalCta from '../../../lp-form-modal-cta';
import * as iconBadge from '../../../icon-badge';
import * as quarkButton from '../../../button';
import * as quarkButtons from '../../../buttons';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Children blocks
 */
import * as overline from './../overline';
import * as heroTitle from './../hero-title';
import * as heroSubtitle from './../hero-subtitle';
import * as heroDescription from './../hero-description';
import * as heroTextGraphic from './../hero-text-graphic';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'hero__left' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			allowedBlocks: [
				iconBadge.name,
				formModalCta.name,
				overline.name,
				heroTitle.name,
				heroSubtitle.name,
				heroDescription.name,
				heroTextGraphic.name,
				quarkButton.name,
				quarkButtons.name,
			],
			template: [
				[ overline.name ],
				[ heroTitle.name ],
				[ heroTextGraphic.name ],
				[ heroSubtitle.name ],
				[ heroDescription.name ],
				[ iconBadge.name, { className: 'hero__tag' } ],
				[ formModalCta.name, { className: 'hero__form-modal-cta color-context--dark' } ],
			],
		}
	);

	// Return the block's markup.
	return <div { ...innerBlockProps } />;
}
