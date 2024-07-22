/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import Section from '../../components/section';
import * as item from './children/icon-info-columns-column';

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
		className: classnames( className, 'icon-info-columns' ),
	} );

	// eslint-disable-next-line react-hooks/rules-of-hooks
	const innerBlockProps = useInnerBlocksProps( { ...blockProps }, {
		allowedBlocks: [ item.name ],
		template: [ [ item.name ], [ item.name ], [ item.name ], [ item.name ], [ item.name ] ],

		// @ts-ignore
		orientation: 'horizontal',
	} );

	// Return the block's markup.
	return (
		<Section { ...blockProps } >
			<div { ...innerBlockProps } />
		</Section>
	);
}
