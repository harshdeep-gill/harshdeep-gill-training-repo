/**
 * WordPress dependencies.
 */
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Styles.
 */
import '../../../front-end/components/title-meta/style.scss';

/**
 * Edit component.
 *
 * @param {Object} props Component properties.
 */
export default function Edit( {}: BlockEditAttributes ): JSX.Element {
	// Get the block props.
	const blockProps = useBlockProps(
		{
			className: classnames( 'title-meta' ),
		}
	);

	// Render the block.
	return (
		<div { ...blockProps }>
			<h1 className="title-meta__title">
				{ __( 'John Doe', 'qrk' ) }
			</h1>
			<p className="title-meta__meta h5">
				{ __( '(Roles will appear here)', 'qrk' ) }
			</p>
		</div>
	);
}
