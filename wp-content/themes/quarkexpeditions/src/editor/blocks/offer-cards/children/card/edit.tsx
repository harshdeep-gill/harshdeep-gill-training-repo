/**
 * WordPress dependencies.
 */
import { RichText, useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child blocks.
 */
import * as title from '../title';
import * as promotion from '../promotion';
import * as help from '../help';
import * as cta from '../cta';

/**
 * Edit component.
 *
 * @param {Object} props               Component properties.
 * @param {Object} props.className     Class name.
 * @param {Object} props.attributes    Block attributes.
 * @param {Object} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'offer-cards__card' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {},
		{
			allowedBlocks: [
				title.name, promotion.name, help.name, cta.name,
			],
			template: [
				[ title.name ],
				[ promotion.name ],
				[ cta.name ],
				[ help.name ],
			],
		},
	);

	// Return the block's markup.
	return (
		<article { ...blockProps }>
			<RichText
				tagName="div"
				className="offer-cards__heading overline"
				placeholder={ __( 'Write Heading Text…', 'qrk' ) }
				value={ attributes.heading }
				onChange={ ( heading: string ) => setAttributes( { heading } ) }
				allowedFormats={ [] }
			/>
			<div className="offer-cards__content">
				<div { ...innerBlockProps } />
			</div>
		</article>
	);
}
