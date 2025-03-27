<!-- resources/js/components/connection-list/dropdown-menu/DropdownMenu.vue -->
<script setup lang="ts">
import {
  DropdownMenuRoot,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from 'radix-vue';
import { Menu } from 'lucide-vue-next';

defineProps<{
  triggerClass?: string;
}>();
</script>

<template>
  <DropdownMenuRoot>
    <DropdownMenuTrigger as-child>
      <button
        :class="[
          'transition duration-200 flex items-center justify-center rounded-xl bg-accent hover:bg-neutral-300 text-neutral-800 hover:text-neutral-900 dark:bg-neutral-800 hover:dark:bg-neutral-700 dark:text-neutral-200 hover:dark:text-neutral-100 px-4 py-3 font-medium',
          triggerClass,
        ]"
        title="Mais opções"
      >
        <Menu :size="20" />
      </button>
    </DropdownMenuTrigger>
    <DropdownMenuContent
      class="mt-2 w-48 rounded-xl bg-white dark:bg-neutral-800 shadow-lg border border-gray-200 dark:border-neutral-700 p-1 z-10"
      side="bottom"
      align="end"
    >
      <slot />
    </DropdownMenuContent>
  </DropdownMenuRoot>
</template>