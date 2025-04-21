<!-- resources/js/components/ConnectionMenu.vue -->
<script setup lang="ts">
import { ButtonNew } from '@/components/connection-menu/button-new';
import { InputSearch } from '@/components/connection-menu/input-search';
import { FilterDropdown } from '@/components/connection-menu/filter-dropdown';
import { ref } from 'vue';
import { RefreshCw } from 'lucide-vue-next'; // Ícone de refresh

// Defina uma interface para o tipo de dados da conexão
interface ConnectionData {
  name: string;
  webhook_url: string;
  is_active: boolean;
}

const props = defineProps<{
  totalConnections: number;
  maxConnections: number;
  isLoading: boolean;
}>();

const emit = defineEmits(['create', 'refresh', 'update:search', 'update:filter']);
const createModalRef = ref(null);

// Função para lidar com a criação da conexão
const handleCreate = (connectionData: ConnectionData) => {
  emit('create', connectionData);
};

// Função para disparar o refresh
const handleRefresh = () => {
  emit('refresh');
};

const handleSearch = (query: string) => {
  emit('update:search', query);
};

const handleFilter = (value: string) => {
  emit('update:filter', value);
};

defineExpose({
  createModalRef,
});
</script>

<template>
  <div class="flex w-full flex-col gap-4 wrapper">
    <!-- Button and Connection Count Row -->
    <div class="flex w-full justify-between items-center top-row">
      <ButtonNew @create="handleCreate" ref="createModalRef" />
      <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300 text-sm sm:text-base whitespace-nowrap sm:mr-5">
        <span title="Quantidade de conexões">{{ totalConnections }} de {{ maxConnections }}</span>
        <FilterDropdown @update:filter="handleFilter" />
        <button
          @click="handleRefresh"
          class="p-1 rounded-full transition-colors h-[45px] w-[45px] flex items-center justify-center"
          :disabled="isLoading"
          title="Atualizar conexões"
        >
          <RefreshCw
            v-if="!isLoading"
            :size="16"
            class="text-gray-500 dark:text-gray-400"
          />
          <span
            v-else
            class="inline-block w-4 h-4 border-2 mb-[-2px] border-gray-500 border-t-transparent rounded-full animate-spin"
          ></span>
        </button>
      </div>
    </div>

    <!-- Search Input Row (adjusted for desktop) -->
    <div class="w-full sm:w-auto search-container">
      <InputSearch @update:search="handleSearch" />
    </div>
  </div>
</template>

<style scoped>
.wrapper {
  /* Default mobile layout is handled by Tailwind (flex-col) */
}

/* Desktop layout adjustments */
@media (min-width: 640px) {
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
    gap: 1rem;
  }

  .search-container {
    margin-left: auto;
  }
}
</style>