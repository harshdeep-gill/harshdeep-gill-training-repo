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
import '../../../front-end/components/staff-member-name-and-roles/style.scss';

/**
 * Edit component.
 *
 * @param {Object} props Component properties.
 */
export default function Edit( {}: BlockEditAttributes ): JSX.Element {
	// Get the block props.
	const blockProps = useBlockProps(
		{
			className: classnames( 'staff-member-name-and-roles' ),
		}
	);

	// Render the block.
	return (
		<div { ...blockProps }>
			<h1 className="staff-member-name-and-roles__title">
				{ __( 'John Doe', 'qrk' ) }
			</h1>
			<p className="staff-member-name-and-roles__roles h5">
				{ __( '(Roles will appear here)', 'qrk' ) }
			</p>
		</div>
	);
}
