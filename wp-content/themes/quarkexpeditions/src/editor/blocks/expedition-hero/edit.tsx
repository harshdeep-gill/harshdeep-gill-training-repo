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

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Children blocks
 */
import * as expeditionHeroContent from './children/content';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component properties.
 * @param {string}   props.className     Class name.
 * @param {Array}    props.attributes    Block attributes.
 * @param {Function} props.setAttributes Set block attributes.
 */
export default function edit( { className }: BlockEditAttributes ): JSX.Element {
	// Set block props.
	const blockProps = useBlockProps( {
		className: classnames(
			className,
			'section',
			'section--has-background',
			'section--has-background-black',
			'section--seamless-with-padding',
		),
	} );

	// Set inner block props.
	const innerBlockProps = useInnerBlocksProps(
		{ className: 'expedition-hero__inner' },
		{
			allowedBlocks: [ expeditionHeroContent.name ],
			template: [ [ expeditionHeroContent.name ] ],
		}
	);

	// Return the block's markup.
	return (
		<Section { ...blockProps } fullWidth={ true } seamless={ true } >
			<div { ...innerBlockProps } />
		</Section>
	);
}
