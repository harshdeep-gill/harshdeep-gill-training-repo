/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Placeholder } from '@wordpress/components';

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
export default function Edit( { className }: BlockEditAttributes ) {
	// Build block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'press-releases' ),
	} );

	// Return the block's markup.
	return (
		<div { ...blockProps }>
			<Placeholder
				icon="welcome-write-blog"
				label={ __( 'Press Releases', 'qrk' ) }
				instructions={ __( 'The press releases will render on the front-end', 'qrk' ) }
			/>
		</div>
	);
}
