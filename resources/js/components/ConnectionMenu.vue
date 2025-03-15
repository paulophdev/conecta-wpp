<script setup lang="ts">
import { ButtonNew } from '@/components/connection-menu/button-new';
import { InputSearch } from '@/components/connection-menu/input-search';

defineEmits(['create']);
</script>

<template>
  <div class="flex w-full flex-col gap-4 wrapper">
    <!-- Button and Connection Count Row -->
    <div class="flex w-full justify-between items-center top-row">
      <ButtonNew @create="$emit('create', $event)" />
      <div class="text-gray-700 dark:text-gray-300 text-sm sm:text-base whitespace-nowrap sm:mr-5">
        1 de 10 Conexões
      </div>
    </div>

    <!-- Search Input Row (adjusted for desktop) -->
    <div class="w-full sm:w-auto search-container">
      <InputSearch />
    </div>
  </div>
</template>

<style scoped>
.wrapper {
  /* Default mobile layout is handled by Tailwind (flex-col) */
}

/* Desktop layout adjustments */
@media (min-width: 640px) {
  /* Matches Tailwind's 'sm' breakpoint */
  .wrapper {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: 0;
  }

  .top-row {
    display: flex;
    align-items: center;
    gap: 1rem; /* Matches Tailwind's gap-4 */
  }

  .search-container {
    margin-left: auto; /* Pushes search to the right */
  }
}
</style>