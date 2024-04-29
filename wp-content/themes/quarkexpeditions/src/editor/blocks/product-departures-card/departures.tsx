/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	useInnerBlocksProps,
	InnerBlocks,
} from '@wordpress/block-editor';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child blocks.
 */
import * as dates from './dates';

/**
 * Register child block.
 */
registerBlockType( dates.name, dates.settings );

/**
 * Block name.
 */
export const name: string = 'quark/product-departures-card-departures';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Card Departures', 'qrk' ),
	description: __( 'Departures section for Product Departures Card', 'qrk' ),
	parent: [ 'quark/product-departures-card' ],
	icon: 'screenoptions',
	category: 'layout',
	keywords: [ __( 'departures', 'qrk' ) ],
	attributes: {
		overline: {
			type: 'string',
			default: '',
		},
	},
	supports: {
		alignWide: false,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
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
	},
	save() {
		// Save InnerBlocks Content.
		return <InnerBlocks.Content />;
	},
};
