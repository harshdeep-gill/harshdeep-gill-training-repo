/**
 * WordPress dependencies.
 */
import { InnerBlocks, useBlockProps, useInnerBlocksProps } from "@wordpress/block-editor";

/**
 * External dependencies.
 */
import classnames from "classnames";

/**
 * Child blocks.
 */
import * as secondaryNavigationItem from '../secondary-navigation-item';

/**
 * Edit Component.
 */
export default function Edit( { className }: BlockEditAttributes ) {
    const blockProps = useBlockProps( {
        className: classnames( className, 'secondary-navigation__navigation' ),
    } );

    const innerBlockProps = useInnerBlocksProps(
        {
            className: classnames( 'secondary-navigation__navigation-items' ),
        },
        {
            allowedBlocks: [ secondaryNavigationItem.name ],
            template: [
                [ secondaryNavigationItem.name, { placeholder: 'Secondary Navigation Item…' } ],
                [ secondaryNavigationItem.name, { placeholder: 'Secondary Navigation Item…' } ],
                [ secondaryNavigationItem.name, { placeholder: 'Secondary Navigation Item…' } ],
            ],
            renderAppender: InnerBlocks.DefaultBlockAppender,
        }
    );

    return (
        <nav { ...blockProps } >
            <ul { ...innerBlockProps } />
        </nav>
    );
}