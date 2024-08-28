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

const COMPONENT = 'staff-member-name-and-roles';

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
	$name  = $staff_member['post_meta']['first_name'] ?? '';
	$name .= $name ? ' ' . ( $staff_member['post_meta']['last_name'] ?? '' ) : '';

	// Fall back to post title if name is empty.
	if ( empty( $name ) ) {
		$name = $staff_member['post']->post_title;
	}

	// Prepare roles.
	$departments = get_departments( $staff_member['post']->ID );
	$roles       = $departments ? implode( ', ', array_column( $departments, 'name' ) ) : '';

	// Build component attributes.
	$attributes = [
		'name'  => $name,
		'roles' => $roles,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
