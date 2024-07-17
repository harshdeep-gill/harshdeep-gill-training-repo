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
 * Edit Component.
 *
 * @param {Object} props           Component properties.
 * @param {string} props.className Class name.
 */
export default function Edit( { className }: BlockEditAttributes ): JSX.Element {
	// eslint-disable-next-line react-hooks/rules-of-hooks
	const blockProps = useBlockProps( { className } );

	// Return the block's markup.
	return (
		<div { ...blockProps }>
			<Placeholder
				label={ __( 'Two Step Form - Step Two Modal Form', 'qrk' ) }
				icon="layout"
			>
				<p>{ __( 'This form will render on the front-end.', 'qrk' ) }</p>
			</Placeholder>
		</div>
	);
}
