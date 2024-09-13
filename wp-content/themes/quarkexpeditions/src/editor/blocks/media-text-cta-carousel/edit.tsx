/**
 * WordPress dependencies
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import * as mediaTextCta from '../media-text-cta';
import Section from '../../components/section';

/**
 * Edit component.
 *
 * @param {Object} props           Component properties.
 * @param {Object} props.className Class name.
 */
export default function edit( { className }: BlockEditAttributes ): JSX.Element {
	// Set the block props.
	const blockProps = useBlockProps( {
		className: classnames( className, 'media-text-cta-carousel' ),
	} );

	// Set the inner blocks props.
	const innerBlockProps = useInnerBlocksProps( {
		className: classnames( 'media-text-cta-carousel__slides' ),
	},
	{
		allowedBlocks: [ mediaTextCta.name ],
		template: [
			[ mediaTextCta.name ],
			[ mediaTextCta.name ],
		],
	} );

	// Return the block's markup.
	return (
		<Section>
			<div { ...blockProps } >
				<div className={ 'media-text-cta-carousel__track' }>
					<div { ...innerBlockProps } />
				</div>
			</div>
		</Section>
	);
}
