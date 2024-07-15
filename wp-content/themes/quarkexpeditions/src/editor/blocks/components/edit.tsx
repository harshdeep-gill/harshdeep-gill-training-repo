/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	Placeholder,
} from '@wordpress/components';
import {
	useBlockProps,
} from '@wordpress/block-editor';

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
		className: classnames( className ),
	} );

	// Return the block's markup.
	return (
		<div { ...blockProps }>
			<Placeholder
				label={ __( 'Components', 'qrk' ) }
				icon="layout"
			>
				<p>{ __( 'All the components available on this website.', 'qrk' ) }</p>
			</Placeholder>
		</div>
	);
}
