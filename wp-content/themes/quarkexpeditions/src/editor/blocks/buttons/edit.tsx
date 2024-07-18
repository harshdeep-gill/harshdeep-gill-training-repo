/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	BlockControls,
} from '@wordpress/block-editor';
import { ToolbarGroup, ToolbarDropdownMenu } from '@wordpress/components';
import {
	justifyLeft,
	justifyCenter,
	justifyRight,
	justifySpaceBetween,
} from '@wordpress/icons';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Internal dependencies.
 */
import icons from '../icons';

/**
 * Edit Component.
 *
 * @param {Object}   props               Component props.
 * @param {Object}   props.attributes    Component attributes.
 * @param {Function} props.setAttributes Component set attributes.
 */
export default function Edit( { attributes, setAttributes }: BlockEditAttributes ): JSX.Element {
	// Block attributes.
	const {
		horizontalAlignment = 'left',
		verticalAlignment = 'middle',
	}: {
		horizontalAlignment?: 'left' | 'center' | 'right' | 'space-between';
		verticalAlignment?: 'top' | 'middle' | 'bottom';
	} = attributes;

	// Block props.
	const blockProps = useBlockProps( {
		className: classnames(
			'buttons',
			`buttons--horizontal-${ horizontalAlignment }`,
			`buttons--vertical-${ verticalAlignment }`,
		),
	} );

	// Inner blocks props.
	const innerBlocksProps = useInnerBlocksProps(
		{ ...blockProps },
		{
			template: [ [ 'quark/button' ] ],
			templateLock: false,
			orientation: 'horizontal',
		},
	);

	// Horizontal alignment options.
	const horizontalAlignmentOptions = [
		{
			name: 'left',
			icon: justifyLeft,
			title: __( 'Align left', 'qrk' ),
			isActive: 'left' === horizontalAlignment,
			onClick: () => setAttributes( { horizontalAlignment: 'left' } ),
		},
		{
			name: 'center',
			icon: justifyCenter,
			title: __( 'Align center', 'qrk' ),
			isActive: 'center' === horizontalAlignment,
			onClick: () => setAttributes( { horizontalAlignment: 'center' } ),
		},
		{
			name: 'right',
			icon: justifyRight,
			title: __( 'Align right', 'qrk' ),
			isActive: 'right' === horizontalAlignment,
			onClick: () => setAttributes( { horizontalAlignment: 'right' } ),
		},
		{
			name: 'space-between',
			icon: justifySpaceBetween,
			title: __( 'Space between', 'qrk' ),
			isActive: 'space-between' === horizontalAlignment,
			onClick: () => setAttributes( { horizontalAlignment: 'space-between' } ),
		},
	];

	// Vertical alignment options.
	const verticalAlignmentOptions = [
		{
			name: 'top',
			icon: icons.top,
			title: __( 'Align top', 'qrk' ),
			isActive: 'top' === verticalAlignment,
			onClick: () => setAttributes( { verticalAlignment: 'top' } ),
		},
		{
			name: 'middle',
			icon: icons.middle,
			title: __( 'Align middle', 'qrk' ),
			isActive: 'middle' === verticalAlignment,
			onClick: () => setAttributes( { verticalAlignment: 'middle' } ),
		},
		{
			name: 'bottom',
			icon: icons.bottom,
			title: __( 'Align bottom', 'qrk' ),
			isActive: 'bottom' === verticalAlignment,
			onClick: () => setAttributes( { verticalAlignment: 'bottom' } ),
		},
	];

	// Horizontal icon.
	let horizontalIcon;
	switch ( horizontalAlignment ) {
		case 'left':
			horizontalIcon = justifyLeft;
			break;
		case 'center':
			horizontalIcon = justifyCenter;
			break;
		case 'right':
			horizontalIcon = justifyRight;
			break;
		case 'space-between':
			horizontalIcon = justifySpaceBetween;
			break;
	}

	// Vertical icon.
	let verticalIcon;
	switch ( verticalAlignment ) {
		case 'top':
			verticalIcon = icons.top;
			break;
		case 'middle':
			verticalIcon = icons.middle;
			break;
		case 'bottom':
			verticalIcon = icons.bottom;
			break;
	}

	// Return block.
	return (
		<>
			<BlockControls group="block" controls={ [] }>
				<ToolbarGroup>
					<ToolbarDropdownMenu
						icon={ horizontalIcon }
						label={ __( 'Horizontal Alignment', 'lb' ) }
						controls={ horizontalAlignmentOptions }
					/>
					<ToolbarDropdownMenu
						icon={ verticalIcon }
						label={ __( 'Vertical Alignment', 'lb' ) }
						controls={ verticalAlignmentOptions }
					/>
				</ToolbarGroup>
			</BlockControls>
			<div { ...innerBlocksProps } />
		</>
	);
}
