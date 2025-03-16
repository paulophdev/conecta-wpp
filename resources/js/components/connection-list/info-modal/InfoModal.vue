<!-- resources/js/components/connection-list/ConnectionModal.vue -->
<script setup lang="ts">
import {
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogOverlay,
  DialogPortal,
  DialogRoot,
  DialogTitle,
} from 'radix-vue';
import { X } from 'lucide-vue-next';

interface Props {
  open: boolean;
  is_active: boolean;
  name: string;
  public_token: string;
  loading?: boolean;
  status?: {
    status: string;
    qrcode?: string;
    urlcode?: string;
    version?: string;
    session?: string;
  };
}

defineProps<Props>();
const emit = defineEmits(['update:open']);
</script>

<template>
  <DialogRoot :open="open" @update:open="$emit('update:open', $event)">
    <DialogPortal>
      <DialogOverlay class="bg-blackA9 data-[state=open]:animate-overlayShow fixed inset-0 z-30" />
      <DialogContent
        class="data-[state=open]:animate-contentShow fixed top-[50%] left-[50%] max-h-[85vh] w-[90vw] max-w-[450px] translate-x-[-50%] translate-y-[-50%] rounded-[20px] bg-white p-[30px] shadow-[hsl(206_22%7%/_35%)_0px_10px_38px_-10px,_hsl(206_22%7%/_20%)_0px_10px_20px_-15px] focus:outline-none z-[100] font-sans"
      >
        <DialogTitle class="text-mauve12 m-0 text-[17px] font-semibold">
          {{ is_active ? 'Detalhes da Conexão' : 'QR Code da Conexão' }}
        </DialogTitle>
        <DialogDescription class="text-mauve11 mt-[10px] mb-5 text-[15px] leading-normal">
          {{ is_active ? 'Informações detalhadas da conexão ativa.' : 'Escaneie o QR Code para ativar a conexão.' }}
        </DialogDescription>

        <div v-if="loading" class="text-center text-black">
            Carregando informações. Aguarde!
        </div>
        <div v-else class="flex flex-col gap-4 text-black">
          <!-- Exibir detalhes ou QR Code -->
          <div v-if="is_active || status?.status === 'CONNECTED'">
            <p>Conectado</p>
          </div>
          <div v-else-if="status?.qrcode">
            <img :src="status.qrcode" alt="QR Code" class="w-full" />
          </div>
          <div v-else>
            <p>Carregando QR Code. Aguarde!</p>
          </div>
        </div>

        <DialogClose
          class="text-black dark:text-gray-400 absolute top-[10px] right-[10px] inline-flex h-[25px] w-[25px] appearance-none items-center justify-center rounded-full focus:ring-2 focus:ring-[#2d79f3] focus:outline-none transition-colors"
          aria-label="Fechar"
          title="Fechar"
        >
          <X :size="20" />
        </DialogClose>
      </DialogContent>
    </DialogPortal>
  </DialogRoot>
</template>