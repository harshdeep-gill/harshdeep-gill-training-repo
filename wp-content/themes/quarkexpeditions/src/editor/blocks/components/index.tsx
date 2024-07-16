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
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/components';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Components', 'qrk' ),
	description: __( 'Display all the components of the website.', 'qrk' ),
	category: 'design',
	keywords: [
		__( 'components', 'qrk' ),
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
	},
	save() {
		// Don't save anything.
		return null;
	},
};
