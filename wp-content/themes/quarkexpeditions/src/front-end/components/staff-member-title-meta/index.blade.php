@props( [
	'name'  => '',
	'roles' => '',
] )

<h1 class="staff-member-title">
	{{ $name ?? '' }}
</h1>

@if ( ! empty( $roles ) )
	<p class="staff-member-roles h5">
		{{ $roles ?? '' }}
	</p>
@endif
