<!-- resources/js/pages/Connections.vue -->
<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { CardActions } from '@/components/ui/card-actions';
import ConnectionMenu from '@/components/ConnectionMenu.vue';
import axios, { AxiosError } from 'axios';

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

// Criar uma cópia reativa da lista de conexões
const localConnections = ref(props.connections);

onMounted(() => {
  // Garantir que a lista local seja inicializada com os dados da prop
  localConnections.value = props.connections;
});

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
      console.log('Nova conexão criada:', data);
      
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
</script>

<template>
  <Head title="Conexões" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Menu de ações -->
    <div class="w-full p-4">
      <ConnectionMenu @create="handleCreateConnection" ref="connectionMenuRef" />
    </div>

    <template v-if="localConnections.length === 0">
      <div class="w-full h-full flex items-center justify-center">
        <p class="text-gray-500 text-center">Nenhuma conexão encontrada.</p>
      </div>
    </template>

    <!-- Lista de conexões -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 h-full p-4">
      <template v-for="connection in localConnections" :key="connection.id">
        <CardActions v-bind="connection" />
      </template>
    </div>
  </AppLayout>
</template>