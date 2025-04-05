<!-- resources/js/components/connection-menu/filter-dropdown/FilterDropdown.vue -->
<script setup lang="ts">
import { ref } from 'vue';
import {
  DropdownMenuRoot,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from 'radix-vue';
import { ListFilter, Blend, MessageCircle, MessageCircleOff } from 'lucide-vue-next';

const emit = defineEmits(['update:filter']);
const selectedFilter = ref('all');

const filters = [
    { label: 'Todos', value: 'all', icon: Blend },
    { label: 'Conectados', value: 'connected', icon: MessageCircle },
    { label: 'Desconectados', value: 'disconnected', icon: MessageCircleOff },
];

const handleFilterChange = (value: string) => {
  selectedFilter.value = value;
  emit('update:filter', value);
};
</script>

<template>
  <DropdownMenuRoot>
    <DropdownMenuTrigger as-child>
      <button
        class="px-2 pl-5 text-gray-700 dark:text-gray-300 rounded-md transition-colors flex items-center gap-2"
        title="Filtros de listagem"
      >
        {{ filters.find(f => f.value === selectedFilter)?.label || 'Filtrar' }}
        <ListFilter />
      </button>
    </DropdownMenuTrigger>

    <DropdownMenuContent
      class="min-w-[150px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg p-1 z-[1000]"
    >
      <DropdownMenuItem
        v-for="filter in filters"
        :key="filter.value"
        @click="handleFilterChange(filter.value)"
        class="px-2 py-1 text-gray-900 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer rounded-sm flex items-center gap-2"
      >
        <component :is="filter.icon" :size="16" class="text-gray-500 dark:text-gray-400" />
        {{ filter.label }}
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenuRoot>
</template>