/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	RichText,
} from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Child block.
 */
import * as item from './children/item';

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
		className: classnames( className, 'menu-list' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( {
		className: classnames( 'menu-list__list' ),
	}, {
		allowedBlocks: [ item.name ],
		template: [ [ item.name ], [ item.name ], [ item.name ] ],
		orientation: 'vertical',
	} );

	// Return the block's markup.
	return (
		<>
			<Section { ...blockProps }>
				<RichText
					tagName="p"
					className="menu-list__title overline"
					placeholder={ __( 'Write title…', 'qrk' ) }
					value={ attributes.title }
					onChange={ ( title: string ) => setAttributes( { title } ) }
					allowedFormats={ [] }
				/>
				<ul { ...innerBlockProps } />
			</Section>
		</>
	);
}
