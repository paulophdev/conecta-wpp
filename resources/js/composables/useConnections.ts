import { ref, computed } from 'vue';
import axios, { AxiosError } from 'axios';
import { useToast } from 'vue-toastification';

interface ConnectionData {
  id: number;
  name: string;
  webhook_url: string;
  private_token: string;
  public_token: string;
  is_active: boolean;
}

interface ValidationErrorResponse {
  success: boolean;
  errors: { [key: string]: string[] };
}

export function useConnections(initialConnections: ConnectionData[], initialTotal: number) {
  const toast = useToast();
  const isLoading = ref(false);
  const localConnections = ref(initialConnections);
  const localTotalConnections = ref(initialTotal);
  const searchQuery = ref('');
  const filterStatus = ref('all');

  const editConnection = async (connectionData: ConnectionData, connectionId: number) => {
    isLoading.value = true;
    try {
      const response = await axios.put(`/connections/${connectionId}`, connectionData, {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        },
      });
      const { success, data } = response.data;
      if (success) {
        localConnections.value = localConnections.value.map(conn => conn.id === data.id ? data : conn);
        toast.success('Conexão atualizada com sucesso!');
        return true;
      }
    } catch (error) {
      toast.error('Erro ao atualizar conexão.');
    } finally {
      isLoading.value = false;
    }
    return false;
  };

  const createConnection = async (connectionData: Omit<ConnectionData, 'id' | 'private_token' | 'public_token'>) => {
    isLoading.value = true;
    try {
      const response = await axios.post('/connections', connectionData, {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
          'Content-Type': 'application/json',
        },
      });

      const { success, message, data } = response.data;

      if (success) {
        toast.success(message || 'Conexão criada com sucesso!');
        localConnections.value = [data, ...localConnections.value];
        localTotalConnections.value += 1;
        return true;
      } else {
        toast.error('Algo deu errado ao criar a conexão.');
      }
    } catch (error) {
      const axiosError = error as AxiosError<ValidationErrorResponse>;
      if (axiosError.response && axiosError.response.status === 422) {
        const errors = axiosError.response.data.errors;
        const errorMessages = Object.values(errors).flat().join('\n');
        toast.error(`Erro ao criar conexão:\n${errorMessages}`);
      } else {
        console.error('Erro ao criar conexão:', axiosError);
        toast.error('Falha ao criar a conexão. Verifique o console para mais detalhes.');
      }
    } finally {
      isLoading.value = false;
    }
    return false;
  };

  const refreshConnections = async () => {
    isLoading.value = true;
    try {
      const response = await axios.get('/connections', {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        },
      });
      localConnections.value = response.data.connections || response.data;
    } catch (error) {
      console.error('Erro ao atualizar conexões:', error);
      toast.error('Falha ao atualizar as conexões.');
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
    localTotalConnections.value -= 1;
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

  return {
    isLoading,
    localConnections,
    localTotalConnections,
    searchQuery,
    filterStatus,
    filteredConnections,
    editConnection,
    createConnection,
    refreshConnections,
    updateConnectionStatus,
    deleteConnection,
  };
} 