/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { Icon } from '@wordpress/components';

/**
 * Styles.
 */
import '../../../front-end/components/breadcrumbs/style.scss';

/**
 * Internal dependencies.
 */
import icons from '../icons';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Block name.
 */
export const name: string = 'quark/breadcrumbs';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Breadcrumbs', 'qrk' ),
	description: __( 'Display a Breadcrumbs block.', 'qrk' ),
	category: 'layout',
	keywords: [
		__( 'breadcrumbs', 'qrk' ),
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
			className: classnames(
				className,
				'breadcrumbs'
			),
		} );

		// Get the chevron icon
		const cheveronIcon = icons.chevronLeft ?? <Icon icon="no" />;

		// Return the block's markup.
		return (
			<div { ...blockProps }>
				<div className="breadcrumbs__breadcrumb">
					<span className="breadcrumbs__breadcrumb-separator">
						{ cheveronIcon }
					</span>
					<span className="breadcrumbs__breadcrumb-title">Home</span>
				</div>
				<div className="breadcrumbs__breadcrumb">
					<span className="breadcrumbs__breadcrumb-separator">
						{ cheveronIcon }
					</span>
					<span className="breadcrumbs__breadcrumb-title">Parent Page</span>
				</div>
				<div className="breadcrumbs__breadcrumb">
					<span className="breadcrumbs__breadcrumb-separator">
						{ cheveronIcon }
					</span>
					<span className="breadcrumbs__breadcrumb-title">Child Page</span>
				</div>
			</div>
		);
	},
	save() {
		// Return null.
		return null;
	},
};
