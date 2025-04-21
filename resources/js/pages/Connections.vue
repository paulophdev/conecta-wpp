<!-- resources/js/pages/Connections.vue -->
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConnectionMenu from '@/components/ConnectionMenu.vue';
import { CreateConnectionModal } from '@/components/connection-menu/create-connection-modal';
import ConnectionList from '@/components/ConnectionList.vue';
import { useConnections } from '@/composables/useConnections';

interface ConnectionData {
  id: number;
  name: string;
  webhook_url: string;
  private_token: string;
  public_token: string;
  is_active: boolean;
}

const props = defineProps<{
  connections: ConnectionData[];
  totalConnections: number;
  maxConnections: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Conexões', href: '/connections' },
];

const connectionMenuRef = ref(null);
const editModalRef = ref(null);
const connectionToEdit = ref<ConnectionData | null>(null);

const {
  isLoading,
  localTotalConnections,
  searchQuery,
  filterStatus,
  filteredConnections,
  editConnection,
  createConnection,
  refreshConnections,
  updateConnectionStatus,
  deleteConnection,
} = useConnections(props.connections, props.totalConnections);

const openEditModal = (connection: ConnectionData) => {
  connectionToEdit.value = connection;
  editModalRef.value?.openModal();
};

const handleCreateConnection = async (connectionData: {
  name: string;
  webhook_url: string;
  is_active: boolean;
}) => {
  await createConnection(connectionData);
  if (connectionMenuRef.value?.createModalRef?.modalRef?.closeModal) {
    connectionMenuRef.value.createModalRef.modalRef.closeModal();
  }
};

const handleEditConnection = async (connectionData: ConnectionData) => {
  if (connectionToEdit.value) {
    await editConnection(connectionData, connectionToEdit.value.id);
    editModalRef.value?.closeModal();
  }
};

const handleSearch = (query: string) => {
  searchQuery.value = query;
};

const handleFilter = (value: string) => {
  filterStatus.value = value;
};
</script>

<template>
  <Head title="Conexões" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Menu de ações -->
    <div class="w-full p-4">
      <ConnectionMenu 
        @create="handleCreateConnection" 
        @refresh="refreshConnections"
        @update:search="handleSearch"
        @update:filter="handleFilter"
        :totalConnections="localTotalConnections"
        :maxConnections="maxConnections"
        :isLoading="isLoading"
        ref="connectionMenuRef"
      />
    </div>

    <template v-if="filteredConnections.length === 0">
      <div class="w-full h-full flex items-center justify-center">
        <p class="text-gray-500 text-center">Nenhuma conexão encontrada.</p>
      </div>
    </template>

    <ConnectionList
      :connections="filteredConnections"
      @open-edit-modal="openEditModal"
      @update:is_active="updateConnectionStatus"
      @delete-connection="deleteConnection"
    />

    <!-- Modal de edição -->
    <CreateConnectionModal
      ref="editModalRef"
      :connection="connectionToEdit"
      @create="handleEditConnection"
    />
  </AppLayout>
</template>