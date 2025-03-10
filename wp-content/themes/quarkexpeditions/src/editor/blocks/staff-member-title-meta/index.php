<?php
/**
 * Block Name: Staff Member Title Meta.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\StaffMemberTitleMeta;

use WP_Post;

use function Quark\StaffMembers\get as get_staff_member;
use function Quark\StaffMembers\get_departments;
use function Quark\StaffMembers\get_roles;

const COMPONENT = 'title-meta';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata(
		__DIR__,
		[
			'render_callback' => __NAMESPACE__ . '\\render',
		]
	);
}

/**
 * Render this block.
 *
 * @param mixed[] $attributes Block attributes.
 * @param string  $content Block default content.
 *
 * @return string
 */
function render( array $attributes = [], string $content = '' ): string {
	// Get current staff member.
	$staff_member = get_staff_member();

	// Check if staff member is empty.
	if ( ! $staff_member['post'] instanceof WP_Post ) {
		return $content;
	}

	// Prepare name.
	$title  = $staff_member['post_meta']['first_name'] ?? '';
	$title .= $title ? ' ' . ( $staff_member['post_meta']['last_name'] ?? '' ) : '';

	// Fall back to post title if name is empty.
	if ( empty( $title ) ) {
		$title = $staff_member['post']->post_title;
	}

	// Prepare roles.
	$roles = get_roles( $staff_member['post']->ID );
	$roles = $roles ? implode( ', ', array_column( $roles, 'name' ) ) : '';

	// Build component attributes.
	$attributes = [
		'title' => $title,
		'meta'  => $roles,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
