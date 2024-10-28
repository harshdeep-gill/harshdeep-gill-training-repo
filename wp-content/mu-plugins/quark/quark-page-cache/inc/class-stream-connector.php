<?php
/**
 * Stream connector.
 *
 * @package quark-page-cache
 */

namespace Quark\PageCache;

use WP_Stream\Connector;

/**
 * Class Stream_Connector.
 */
class Stream_Connector extends Connector {
    /**
     * Connector slug.
     *
     * @var string
     */
    public $name = 'quark_page_cache';

    /**
     * Actions registered for this connector.
     *
     * @var string[]
     */
    public $actions = [
        'quark_page_cache_flushed',
    ];

    /**
     * Return translated connector label.
     *
     * @return string
     */
    public function get_label(): string {
        return __( 'Page Cache', 'qrk' );
    }

    /**
     * Return translated context labels.
     *
     * @return string[]
     */
    public function get_context_labels(): array {
        return [
            'page_cache' => __( 'Page Cache', 'qrk' ),
        ];
    }

    /**
     * Return translated action labels.
     *
     * @return string[]
     */
    public function get_action_labels(): array {
        return [
            'flushed' => __( 'Page Cache Flushed', 'qrk' ),
        ];
    }

    /**
     * Callback for `quark_page_cache_flushed` action.
     *
     * @param mixed[] $data Data passed to the action.
     *
     * @return void
     */
    public function callback_quark_page_cache_flushed( array $data = [] ): void {
        // Validate data.
        if ( empty( $data ) || empty( $data['time_took'] ) || ! is_scalar( $data['time_took'] ) ) {
            return;
        }

        // Format time took to two decimal places.
        $time_took = number_format( floatval( $data['time_took'] ), 3 );

        // Prepare message.
        $message = sprintf(
            __( 'Page cache flushed. Time took: %s seconds.', 'qrk' ),
            $time_took
        );

        // Log action.
        $this->log(
            $message,
            [],
            0,
            'page_cache',
            'flushed'
        );

    }
}
