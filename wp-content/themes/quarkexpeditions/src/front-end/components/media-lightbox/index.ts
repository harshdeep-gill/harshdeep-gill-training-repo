/**
 * Global variables.
 */
const { customElements } = window;

/**
 * Internal dependencies.
 */
import QuarkMediaLightbox from './media-lightbox';
import QuarkWistiaEmbed from './wistia-embed';

/**
 * Initialize.
 */
customElements.define( 'quark-media-lightbox', QuarkMediaLightbox );
customElements.define( 'quark-wistia-embed', QuarkWistiaEmbed );
