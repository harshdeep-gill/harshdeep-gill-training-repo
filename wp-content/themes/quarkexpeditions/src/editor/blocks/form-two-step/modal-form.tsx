/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import {
	Placeholder,
} from '@wordpress/components';
import {
	useBlockProps,
	InnerBlocks,
} from '@wordpress/block-editor';

/**
 * Styles.
 */
import '../../../front-end/components/form-two-step/style.scss';

/**
 * Block name.
 */
export const name: string = 'quark/form-two-step-modal-form';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Two Step Modal Form', 'qrk' ),
	description: __( 'Display the first step - Modal Form', 'qrk' ),
	category: 'forms',
	keywords: [
		__( 'modal', 'qrk' ),
		__( 'form', 'qrk' ),
	],
	attributes: {},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ): JSX.Element {
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
	},
	save() {
		// Don't save anything.
		return <InnerBlocks.Content />;
	},
};
