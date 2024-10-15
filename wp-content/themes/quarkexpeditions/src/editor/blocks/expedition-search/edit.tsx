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
		className: classnames( className, 'expedition-search' ),
	} );

	// Return the block's markup.
	return (
		<div { ...blockProps }>
			<Placeholder
				icon="search"
				label={ __( 'Expedition Search', 'qrk' ) }
				instructions={ __( 'This block will render on the front-end', 'qrk' ) }
			/>
		</div>
	);
}
