/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Styles.
 */
import './editor.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';
const { gumponents } = window;

/**
 * External components.
 */
const { SelectImage } = gumponents.components;

/**
 * Children blocks
 */
import * as authorName from './children/name';
import * as readTime from './children/read-time';

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
		className: classnames(
			className,
			'post-author-info'
		),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps(
		{ className: 'post-author-info__info' },
		{
			allowedBlocks: [ authorName.name, readTime.name ],
			template: [ [ authorName.name ], [ readTime.name ] ],
			templateLock: 'all',
		}
	);

	// Return the block's markup.
	return (
		<div { ...blockProps }>
			<SelectImage
				image={ attributes.authorImage }
				size="thumbnail"
				className="post-author-info__image"
				onChange={ ( authorImage: Object ): void => {
					// Set image.
					setAttributes( { authorImage } );
				} }
			/>
			<div { ...innerBlockProps } />
		</div>
	);
}
