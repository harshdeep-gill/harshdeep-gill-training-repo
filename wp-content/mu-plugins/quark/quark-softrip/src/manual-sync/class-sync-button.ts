/**
 * Class to create sync button.
 *
 * @package quark-softrip
 */

/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { dispatch } from '@wordpress/data';
import {store as noticeStore} from '@wordpress/notices';

/**
 * Global variables.
 */
declare global {
    interface Window {
        quarkSoftripAdmin: {
            type?: string;
            message?: string;
        } | undefined;
    }
}
const { quarkSoftripAdmin } = window;

/**
 * Sync Button.
 */
export class SoftripSyncButton {
    /**
     * Properties.
     */
    private postHeaderToolbarContainer: HTMLElement | null;
     /**
     * Constructor.
     */
     constructor() {
        this.postHeaderToolbarContainer = null;

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
    attachSyncButton(): void {
        this.postHeaderToolbarContainer = document.querySelector('.edit-post-header-toolbar');

        // If post save button is not found, return.
        if (!this.postHeaderToolbarContainer) {
            return;
        }

        // Select template containing sync button.
        const template: HTMLTemplateElement | null = document.querySelector('#quark-softrip-admin-bar-sync-button');

        // Validate template content.
        if (!template || !template.content.children.length) {
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
    displaySyncNotice(): void {
        if (undefined === typeof quarkSoftripAdmin || 'object' !== typeof quarkSoftripAdmin) {
            return;
        }

        // Get the notice type.
        const { type, message } = quarkSoftripAdmin;

        // If notice type or message is not set, return.
        if ( undefined === type || undefined === message ) {
            return;
        }

        // Dispatch.
        dispatch(noticeStore).createNotice(
            type,
            message,
            {
                isDismissible: true,
            }
        );
    }
}