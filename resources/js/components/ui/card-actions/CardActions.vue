<!-- resources/js/components/ui/card-actions/CardActions.vue -->
<script setup lang="ts">
import { cn } from '@/lib/utils';
import { ref } from 'vue';
import type { HTMLAttributes } from 'vue';
import { MessageCircle, MessageCircleOff, Pencil, Waypoints, Info, Power, Trash2, Send } from 'lucide-vue-next';
import { InfoQrcode } from '@/components/connection-list/info-qrcode';
import { InfoModal } from '@/components/connection-list/info-modal';
import { TestMessageModal } from '@/components/connection-list/test-message-modal';
import { DropdownMenu } from '@/components/connection-list/dropdown-menu';
import { CopyTokenButton } from '@/components/connection-list/copy-token-button';
import { DropdownMenuItem } from 'radix-vue';
import axios from 'axios';

interface Props {
  id?: number;
  name?: string;
  webhook_url?: string;
  public_token?: string;
  is_active?: boolean;
  webhook_enable?: boolean | number;
  created_at?: string | null;
  updated_at?: string | null;
  class?: HTMLAttributes['class'];
}

const props = withDefaults(defineProps<Props>(), {
  id: 0,
  name: 'System Update',
  webhook_url: '',
  public_token: '',
  is_active: false,
  webhook_enable: true,
  created_at: null,
  updated_at: null,
});

const webhookEnable = ref(Boolean(props.webhook_enable));
const isLoading = ref(false);
const isModalOpen = ref(false);
const isTestModalOpen = ref(false);
const connectionStatus = ref<any>(null);

const toggleWebhook = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get(`/connection/enable-webhook/${props.id}`, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      },
    });
    webhookEnable.value = response.data.webhook_enable;
    alert(response.data.success || 'Webhook atualizado com sucesso!');
  } catch (error) {
    console.error('Erro ao atualizar webhook:', error);
    alert('Falha ao atualizar o webhook.');
  } finally {
    isLoading.value = false;
  }
};

const fetchConnectionStatus = async () => {
  isLoading.value = true;
  isModalOpen.value = true;
  try {
    const response = await axios.get(`/connections/status/${props.public_token}`, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      },
    });

    const { success, data } = response.data;
    if (success) {
      connectionStatus.value = data;
      emit('update:is_active', data.status.status === 'CONNECTED'); // Atualizar o estado via evento
    } else {
      alert('Erro ao obter o status da conexão.');
    }
  } catch (error) {
    console.error('Erro ao consultar status:', error);
    alert('Falha ao consultar o status da conexão.');
  } finally {
    isLoading.value = false;
  }

  const channel = window.Echo.private(`connection.${props.public_token}`);
  channel.listen('.status-updated', (event: any) => {
    connectionStatus.value = {
      id: event.id,
      name: event.name,
      public_token: event.public_token,
      status: event.status,
      profile: event.profile,
    };
    emit('update:is_active', event.is_active); // Atualizar via WebSocket
  });
};

const closeModal = () => {
  isModalOpen.value = false;
  window.Echo.leave(`connection.${props.public_token}`);
};

const disconnect = async () => {
  if (confirm(`Você tem certeza que deseja desconectar a conexão de ${props.name}?`)) {
    isLoading.value = true;
    try {
      const response = await axios.post(`/connections/${props.id}/disconnect`, {}, {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        },
      });
      if (response.data.success) {
        emit('update:is_active', false); // Notificar o pai para atualizar o estado
        alert('Conexão desconectada com sucesso!');
      }
    } catch (error) {
      console.error('Erro ao desconectar:', error);
      alert('Falha ao desconectar a conexão.');
    } finally {
      isLoading.value = false;
    }
  }
};

const deleteConnection = async () => {
  if (confirm(`Você tem certeza que deseja excluir a conexão de ${props.name}?`)) {
    isLoading.value = true;
    try {
      const response = await axios.delete(`/connections/${props.id}`, {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        },
      });
      if (response.data.success) {
        emit('delete-connection', props.id);
        alert('Conexão excluída com sucesso!');
      }
    } catch (error) {
      console.error('Erro ao excluir:', error);
      alert('Falha ao excluir a conexão.');
    } finally {
      isLoading.value = false;
    }
  }
};

const sendTestMessage = async (data: { phone: string; message: string }) => {
  isLoading.value = true;
  try {
    const response = await axios.post(`/connections/${props.id}/test-message`, {
      phone: data.phone,
      message: data.message,
    }, {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      },
    });
    if (response.data.success) {
      alert('Mensagem de teste enviada com sucesso!');
      isTestModalOpen.value = false;
    }
  } catch (error) {
    console.error('Erro ao enviar mensagem de teste:', error);
    alert('Falha ao enviar a mensagem de teste.');
  } finally {
    isLoading.value = false;
  }
};

const emit = defineEmits(['open-edit-modal', 'update:is_active', 'delete-connection']);
</script>

<template>
  <div :class="cn('group relative w-full', props.class)">
    <div class="relative overflow-hidden rounded-2xl border-2">
      <div class="relative p-6 bg-sidebar">
        <!-- Header -->
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="relative">
              <div class="relative flex h-12 w-12 items-center justify-center rounded-xl bg-accent dark:bg-neutral-800">
                <MessageCircle v-if="props.is_active" />
                <MessageCircleOff v-else />
              </div>
            </div>
            <div>
              <h3 class="font-semibold dark:text-white">{{ name }}</h3>
            </div>
          </div>
          <div class="flex flex-col items-end gap-1">
            <span
              class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium"
              :class="props.is_active ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500'"
            >
              <span
                class="h-1 w-1 rounded-full"
                :class="props.is_active ? 'bg-emerald-500' : 'bg-red-500'"
              ></span>
              {{ props.is_active ? 'Conectado' : 'Desconectado' }}
            </span>
          </div>
        </div>

        <!-- Content -->
        <div class="mt-6 space-y-4">
          <div class="rounded-xl bg-accent dark:bg-neutral-800 p-4">
            <div class="flex items-center gap-3">
              <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-500/10">
                <Waypoints :size="15" />
              </div>
              <p class="text-sm dark:text-slate-400">{{ public_token }}</p>
            </div>
          </div>

          <div class="flex gap-3">
            <InfoQrcode :is_active="props.is_active" @open-modal="fetchConnectionStatus" />
            <DropdownMenu>
              <CopyTokenButton :token="public_token" />
              <DropdownMenuItem
                class="w-full px-4 py-2 text-sm text-neutral-800 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700 flex items-center gap-2 cursor-pointer"
                @click="emit('open-edit-modal', { id, name, webhook_url })"
              >
                <Pencil :size="16" />
                Editar conexão
              </DropdownMenuItem>
              <DropdownMenuItem
                v-if="props.is_active"
                class="w-full px-4 py-2 text-sm text-neutral-800 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700 flex items-center gap-2 cursor-pointer"
                @click="disconnect"
                :disabled="isLoading"
              >
                <Power :size="16" />
                Desconectar
              </DropdownMenuItem>
              <DropdownMenuItem
                class="w-full px-4 py-2 text-sm text-neutral-800 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700 flex items-center gap-2 cursor-pointer"
                @click="deleteConnection"
                :disabled="isLoading"
              >
                <Trash2 :size="16" />
                Excluir
              </DropdownMenuItem>
              <DropdownMenuItem
                v-if="props.is_active"
                class="w-full px-4 py-2 text-sm text-neutral-800 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700 flex items-center gap-2 cursor-pointer"
                @click="isTestModalOpen = true"
                :disabled="isLoading"
              >
                <Send :size="16" />
                Testar conexão
              </DropdownMenuItem>
            </DropdownMenu>
          </div>
        </div>

        <!-- Toggle -->
        <div class="mt-6 flex items-center justify-between rounded-xl bg-sky-900/30 p-4">
          <div class="w-full flex justify-between items-center gap-1">
            <div class="flex items-center gap-2">
              <div class="dark:text-slate-400" title="O envio de webhook é útil para automações, porém requer mais processamento do servidor, o que pode aumentar a instabilidade dos serviços. Utilize com cautela.">
                <Info size="20" />
              </div>
              <div>
                <p class="text-sm font-medium dark:text-white">
                  Envio de webhook: <span v-text="webhookEnable ? 'Ativo' : 'Inativo'"></span>
                </p>
                <p
                  class="text-xs dark:text-slate-400 truncate max-w-[200px] md:max-w-[150px] lg:max-w-[170px] xl:max-w-[200px]"
                  :title="webhook_url"
                >
                  {{ webhook_url }}
                </p>
              </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input
                v-model="webhookEnable"
                type="checkbox"
                class="sr-only peer"
                @change="toggleWebhook"
                :disabled="isLoading"
              />
              <div
                class="w-11 h-6 rounded-full bg-slate-800 transition-colors"
                :class="{ 'bg-emerald-500': webhookEnable, 'opacity-50': isLoading }"
              ></div>
              <div
                class="w-4 h-4 bg-white rounded-full absolute left-1 top-1 transition-transform duration-300"
                :class="{ 'translate-x-5': webhookEnable }"
              ></div>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Info -->
    <InfoModal
      :open="isModalOpen"
      :is_active="props.is_active"
      :name="name"
      :public_token="public_token"
      :status="connectionStatus?.status"
      :profile="connectionStatus?.profile"
      :isLoading="isLoading"
      @update:open="closeModal"
    />

    <!-- Modal de Teste -->
    <TestMessageModal
      :open="isTestModalOpen"
      :connectionId="props.id"
      :isLoading="isLoading"
      @update:open="isTestModalOpen = $event"
      @send-message="sendTestMessage"
    />
  </div>
</template>