/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Styles.
 */
import './editor.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'post-author-info'
		),
	} );

	// Return the block's markup.
	return (
		<div { ...blockProps }>
			<div className="post-author-info__image"></div>
			<div className="post-author-info__info">
				<p className="post-author-info__name">{ __( 'Author Name', 'qrk' ) }</p>
				<p className="post-author-info__duration">{ __( 'X min read', 'qrk' ) }</p>
			</div>
		</div>
	);
}
