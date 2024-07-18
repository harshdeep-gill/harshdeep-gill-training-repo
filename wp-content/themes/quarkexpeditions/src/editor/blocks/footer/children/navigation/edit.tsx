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
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Children blocks
 */
import * as navigationItem from '../navigation-item';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames( className, 'footer__accordion' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps(
		{ className: classnames( className, 'footer__navigation' ) },
		{
			allowedBlocks: [ navigationItem.name ],
			template: [ [ navigationItem.name ], [ navigationItem.name ] ],
		}
	);

	// Return the block's markup.
	return (
		<div { ...blockProps }>
			<p>
				<RichText
					tagName="span"
					className="footer__navigation-title"
					placeholder={ __( 'Write Navigation Title…', 'qrk' ) }
					value={ attributes.title }
					onChange={ ( title: string ) => setAttributes( { title } ) }
					allowedFormats={ [] }
				/>
			</p>
			<ul { ...innerBlockProps } />
		</div>
	);
}
