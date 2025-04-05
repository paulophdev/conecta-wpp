<!-- resources/js/pages/Connections.vue -->
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { CardActions } from '@/components/ui/card-actions';
import ConnectionMenu from '@/components/ConnectionMenu.vue';
import { CreateConnectionModal } from '@/components/connection-menu/create-connection-modal';
import axios, { AxiosError } from 'axios';

interface ConnectionData {
  id: number;
  name: string;
  webhook_url: string;
  private_token: string;
  public_token: string;
  is_active: boolean;
}

const props = defineProps<{
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

interface ValidationErrorResponse {
  success: boolean;
  errors: { [key: string]: string[] };
}

const isLoading = ref(false);
const connectionMenuRef = ref(null);
const editModalRef = ref(null);
const connectionToEdit = ref(null);
const localConnections = ref(props.connections);
const searchQuery = ref('');
const filterStatus = ref('all');

const openEditModal = (connection: ConnectionData) => {
  connectionToEdit.value = connection;
  editModalRef.value?.openModal(); // Abrir o modal
};

const editConnection = async (connectionData: ConnectionData) => {
  isLoading.value = true;
  try {
    const response = await axios.put(`/connections/${connectionToEdit.value.id}`, connectionData, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      },
    });
    const { success, data } = response.data;
    if (success) {
      localConnections.value = localConnections.value.map(conn => conn.id === data.id ? data : conn);
      editModalRef.value?.closeModal();
      alert('Conexão atualizada com sucesso!');
    }
  } catch (error) {
    alert('Erro ao atualizar conexão.');
  } finally {
    isLoading.value = false;
  }
};

// Função para criar a conexão
const handleCreateConnection = async (connectionData: {
  name: string;
  webhook_url: string;
  is_active: boolean;
}) => {
  isLoading.value = true;
  try {
    // Envia os dados diretamente para o backend
    const response = await axios.post('/connections', connectionData, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Content-Type': 'application/json',
      },
    });

    // Tratar a resposta do backend
    const { success, message, data } = response.data;

    if (success) {
      alert(message || 'Conexão criada com sucesso!');
      
      // Adicionar a nova conexão à lista local
      localConnections.value = [data, ...localConnections.value];
      
      // Fechar o modal
      if (connectionMenuRef.value?.createModalRef?.modalRef?.closeModal) {
        connectionMenuRef.value.createModalRef.modalRef.closeModal();
      }
    } else {
      alert('Algo deu errado ao criar a conexão.');
    }

  } catch (error) {
    const axiosError = error as AxiosError<ValidationErrorResponse>;
    if (axiosError.response && axiosError.response.status === 422) {
      const errors = axiosError.response.data.errors;
      const errorMessages = Object.values(errors).flat().join('\n');
      alert(`Erro ao criar conexão:\n${errorMessages}`);
    } else {
      console.error('Erro ao criar conexão:', axiosError);
      alert('Falha ao criar a conexão. Verifique o console para mais detalhes.');
    }
  } finally {
    isLoading.value = false;
  }
};

const updateConnectionStatus = (id: number, isActive: boolean) => {
  localConnections.value = localConnections.value.map(conn =>
    conn.id === id ? { ...conn, is_active: isActive } : conn
  );
};

const deleteConnection = (id: number) => {
  localConnections.value = localConnections.value.filter(conn => conn.id !== id);
};

const refreshConnections = async () => {
  try {
    const response = await axios.get('/connections', {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      },
    });
    localConnections.value = response.data.connections || response.data; // Ajuste conforme a estrutura da resposta
  } catch (error) {
    console.error('Erro ao atualizar conexões:', error);
    alert('Falha ao atualizar as conexões.');
  } finally {
    if (connectionMenuRef.value?.resetRefreshing) {
      connectionMenuRef.value.resetRefreshing(); // Reseta o estado de carregamento
    }
  }
};

const filteredConnections = computed(() => {
  let result = localConnections.value;

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(conn =>
      conn.name.toLowerCase().includes(query) ||
      conn.webhook_url.toLowerCase().includes(query) ||
      conn.public_token.toLowerCase().includes(query)
    );
  }

  if (filterStatus.value === 'connected') {
    result = result.filter(conn => conn.is_active);
  } else if (filterStatus.value === 'disconnected') {
    result = result.filter(conn => !conn.is_active);
  }

  return result;
});

const handleSearch = (query: string) => {
  searchQuery.value = query;
};

const handleFilter = (value: string) => {
  filterStatus.value = value;
};

onMounted(() => {
  // Garantir que a lista local seja inicializada com os dados da prop
  localConnections.value = props.connections;
});
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
        ref="connectionMenuRef"
      />
    </div>

    <template v-if="filteredConnections.length === 0">
      <div class="w-full h-full flex items-center justify-center">
        <p class="text-gray-500 text-center">Nenhuma conexão encontrada.</p>
      </div>
    </template>

    <!-- Lista de conexões -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 h-full p-4">
      <template v-for="connection in filteredConnections" :key="connection.id">
        <CardActions 
          v-bind="connection" 
          @open-edit-modal="openEditModal"
          @update:is_active="updateConnectionStatus(connection.id, $event)"
          @delete-connection="deleteConnection($event)"
        />
      </template>
    </div>

    <!-- Modal de edição -->
    <CreateConnectionModal
      ref="editModalRef"
      :connection="connectionToEdit"
      @create="editConnection"
    />
  </AppLayout>
</template>