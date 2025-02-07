<quark-expedition-search-selected-filters class="expedition-search__selected-filters">
	<template>
		<quark-expedition-search-selected-filter-pill class="expedition-search__selected-filter-pill">
			<span class="expedition-search__selected-filter-text"></span>
			<button class="expedition-search__selected-filter-close"><x-svg name="cross" /></button>
		</quark-expedition-search-selected-filter-pill>
	</template>
	<div class="expedition-search__selected-filters-container">
		<div class="expedition-search__selected-filters-list">
		</div>
		<button class="expedition-search__selected-filters-clear-all">{{ __( 'Clear All', 'qrk' ) }}</button>
	</div>
</quark-expedition-search-selected-filters>
