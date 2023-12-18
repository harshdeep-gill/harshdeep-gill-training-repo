/**
 * WordPress dependencies.
 */
import {
	useBlockProps,
} from '@wordpress/block-editor';
import { forwardRef } from '@wordpress/element';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Section component.
 *
 * @param {Object}  props            Component properties.
 * @param {Object}  props.innerRef   Inner ref.
 * @param {string}  props.className  Class.
 * @param {boolean} props.seamless   Seamless.
 * @param {boolean} props.fullWidth  Full width.
 * @param {boolean} props.narrow     Narrow.
 * @param {boolean} props.background Has background.
 * @param {boolean} props.padding    Has padding.
 * @param {boolean} props.noBorder   Has no border.
 * @param {Object}  props.children   Children.
 *
 * @return {JSX.Element} Section component.
 */
function MySection( {
	innerRef,
	className = '',
	seamless = false,
	fullWidth = false,
	narrow = false,
	background = false,
	padding = false,
	noBorder = false,
	children,
}: any ): JSX.Element {
	// Section component.
	return (
		<div
			{
				// eslint-disable-next-line react-hooks/rules-of-hooks
				...useBlockProps( {
					className: classnames( 'section', className, {
						'section--seamless': seamless,
						'section--narrow': narrow,
						'section--no-border': noBorder,
						'section--has-background': background,
						'section--seamless-with-padding': padding,
						'full-width': fullWidth || background,
					} ),
				} )
			}
			ref={ innerRef }
		>
			{ children }
		</div>
	);
}

/**
 * Forward ref to MySection.
 */
const Section = forwardRef( ( props: any, ref ) => <MySection innerRef={ ref } { ...props } /> );
export default Section;
