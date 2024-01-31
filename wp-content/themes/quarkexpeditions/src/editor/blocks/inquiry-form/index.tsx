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
} from '@wordpress/block-editor';

/**
 * Styles.
 */
import '../../../front-end/components/inquiry-form/style.scss';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'qrk/inquiry-form';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 2,
	title: __( 'Inquiry Form', 'qrk' ),
	description: __( 'Display an inquiry form.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'inquiry', 'qrk' ),
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
		const blockProps = useBlockProps( {
			className: classnames( className, 'inquiry-form' ),
		} );

		// Return the block's markup.
		return (
			<>
				<div { ...blockProps }>
					<Placeholder
						label={ __( 'Inquiry Form', 'qrk' ) }
						icon="layout"
					>
						<p>{ __( 'This form will render on the front-end.', 'qrk' ) }</p>
					</Placeholder>
				</div>
			</>
		);
	},
	save() {
		// Don't save anything.
		return null;
	},
};
