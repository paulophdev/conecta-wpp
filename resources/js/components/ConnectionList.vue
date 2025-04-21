<!-- resources/js/components/ConnectionList.vue -->
<script setup lang="ts">
import { CardActions } from '@/components/ui/card-actions';
import { type ConnectionData } from '@/types';

interface Props {
  connections: ConnectionData[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'open-edit-modal', connection: ConnectionData): void;
  (e: 'update:is_active', id: number, isActive: boolean): void;
  (e: 'delete-connection', id: number): void;
}>();
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 h-full p-4">
    <template v-for="connection in connections" :key="connection.id">
      <CardActions 
        v-bind="connection" 
        @open-edit-modal="emit('open-edit-modal', connection)"
        @update:is_active="emit('update:is_active', connection.id, $event)"
        @delete-connection="emit('delete-connection', $event)"
      />
    </template>
  </div>
</template> 