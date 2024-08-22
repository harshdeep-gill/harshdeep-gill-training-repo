/**
 * Sync button.
 *
 * @package quark-softrip
 */

/**
 * Global variables
 */
const {wp} = window;
const { __ } = wp.i18n;

/**
 * Sync button.
 */
class SyncButton {
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

        // Select JS element having sync button html.
        this.syncButton = document.querySelector('#quark-softrip-sync-admin');

        // If sync button is not found, return.
        if (!this.syncButton || !this.syncButton.innerHTML) {
            return;
        }

        // Create template element and set innerHTML.
        const template = document.createElement('template');
        template.innerHTML = this.syncButton.innerHTML;

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

new SyncButton();