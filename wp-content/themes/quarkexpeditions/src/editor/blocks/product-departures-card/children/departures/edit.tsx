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
import * as dates from '../dates';

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
		className: classnames( className, 'product-departures-card__departures' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps },
		{
			allowedBlocks: [ dates.name ],
			template: [ [ dates.name ], [ dates.name ] ],
		},
	);

	// Return the block's markup.
	return (
		<div { ...blockProps } >
			<RichText
				tagName="div"
				className="product-departures-card__overline"
				placeholder={ __( 'Write Departures Section Title…', 'qrk' ) }
				value={ attributes.overline }
				onChange={ ( overline: string ) => setAttributes( { overline } ) }
				allowedFormats={ [] }
			/>
			<div { ...innerBlockProps } />
		</div>
	);
}
