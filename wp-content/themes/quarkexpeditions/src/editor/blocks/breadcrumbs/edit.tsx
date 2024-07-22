/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import { Icon } from '@wordpress/components';

/**
 * Internal dependencies.
 */
import icons from '../icons';

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
}
