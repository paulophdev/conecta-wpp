<!-- resources/js/pages/Connections.vue -->
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { CardActions } from '@/components/ui/card-actions';
import ConnectionMenu from '@/components/ConnectionMenu.vue';

defineProps<{
  connections: Array<{
    id: number;
    name: string;
    webhook_url: string;
    private_token: string;
    public_token: string;
    is_active: boolean;
  }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Conexões', href: '/connections' },
];

const handleCreateConnection = (connectionData: {
  name: string;
  webhook_url: string;
  is_active: boolean;
}) => {
  // Aqui você implementaria a lógica para criar a conexão
  // Por exemplo, usando Inertia.js para enviar ao backend:
  // Inertia.post('/connections', connectionData);
  console.log('Nova conexão:', connectionData);
};
</script>

<template>
  <Head title="Conexões" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Menu de ações -->
    <div class="w-full p-4">
      <ConnectionMenu @create="handleCreateConnection" />
    </div>

    <template v-if="connections.length === 0">
      <div class="w-full h-full flex items-center justify-center">
        <p class="text-gray-500 text-center">Nenhuma conexão encontrada.</p>
      </div>
    </template>

    <!-- Lista de conexões -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 h-full p-4">
      <template v-for="connection in connections" :key="connection.id">
        <CardActions v-bind="connection" />
      </template>
    </div>
  </AppLayout>
</template>