/**
 * Sync button.
 *
 * @package quark-softrip
 */

/**
 * Global variables
 */
const { wp } = window;
const { __ } = wp.i18n;

/**
 * Sync button.
 */
class SoftripSyncButton {
    /**
     * Constructor.
     */
    constructor() {
        // Attach sync button.
        setTimeout(() => {
            this.attachSyncButton()
        }, 500);

        // Display sync notice.
        this.displaySyncNotice();
    }

    /**
     * Attach sync button.
     *
     * @return {void}
    */
    attachSyncButton() {
        this.postHeaderToolbarContainer = document.querySelector('.edit-post-header-toolbar');

        // If post save button is not found, return.
        if (!this.postHeaderToolbarContainer) {
            return;
        }

        // Select template containing sync button.
        const template = document.querySelector('#quark-softrip-admin-bar-sync-button');

        // Validate template content.
        if (!template.content.children.length) {
            return;
        }

        // Insert sync button at last.
        this.postHeaderToolbarContainer.insertAdjacentElement('afterend', template.content.children[0]);
    }

    /**
     * Display sync notice.
     *
     * @return {void}
     */
    displaySyncNotice() {
        if ('undefined' === window.quarkSoftripAdmin || 'object' !== typeof window.quarkSoftripAdmin) {
            return;
        }

        // Get the notice type.
        const { type, message } = window.quarkSoftripAdmin;

        // If notice type or message is not set, return.
        if ( 'undefined' === type || 'undefined' === message ) {
            return;
        }

        // Get the dispatch function.
        const { dispatch } = wp.data;

        // Dispatch.
        dispatch('core/notices').createNotice(
            type,
            message,
            {
                isDismissible: true,
            }
        );
    }
}

new SoftripSyncButton();