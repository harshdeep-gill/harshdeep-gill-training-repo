/**
 * WordPress dependencies.
 */
import { InspectorControls, RichText, useBlockProps } from "@wordpress/block-editor";
import { PanelBody } from "@wordpress/components";
import { __ } from "@wordpress/i18n";

/**
 * External dependencies.
 */
import classNames from "classnames";
const { gumponents } = window;

/**
 * External components.
 */
const { LinkControl } = gumponents.components;

/**
 * Edit Component.
 */
export default function Edit( { className, attributes, setAttributes }: BlockEditAttributes ) {
    const blockProps = useBlockProps( {
        className: classNames( className, 'secondary-navigation__navigation-item' ),
    } );

    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Options', 'qrk' ) }>
                    <LinkControl
                        label={ __( 'Enter URL', 'qrk' ) }
                        value={ attributes.url }
                        help={ __( 'Enter a URL for this navigation item', 'qrk' ) }
                        onChange={ ( url: object ) => setAttributes( { url } ) }
                    />
                </PanelBody>
            </InspectorControls>
            <li { ...blockProps }>
                <RichText
                    tagName="a"
                    className="secondary-navigation__navigation-item-link"
                    placeholder={ __( 'Navigation Item…', 'qrk' ) }
                    value={ attributes.title }
                    onChange={ ( title: string ) => setAttributes( { title } ) }
                />
            </li>
        </>
    );
}