<!-- resources/js/components/connection-list/test-message-modal/TestMessageModal.vue -->
<script setup lang="ts">
import { DialogRoot, DialogContent, DialogTitle, DialogDescription, DialogClose, DialogOverlay } from 'radix-vue';
import { X, Phone, MessageSquare } from 'lucide-vue-next';
import { ref } from 'vue';
import { useToast } from 'vue-toastification';

defineProps<{
  open: boolean;
  connectionId: number;
  isLoading: boolean;
}>();

const emit = defineEmits(['update:open', 'send-message']);

const testPhoneNumber = ref('');
const testMessage = ref('Essa é uma mensagem de teste!');

const toast = useToast();

const sendTestMessage = async () => {
  if (!testPhoneNumber.value) {
    toast.error('Por favor, insira um número de telefone.');
    return;
  }
  // Remover caracteres não numéricos antes de enviar
  const cleanPhone = testPhoneNumber.value.replace(/\D/g, '');
  emit('send-message', {
    phone: cleanPhone,
    message: testMessage.value,
  });
};

// Fechar o modal e resetar os campos
const closeModal = () => {
  testPhoneNumber.value = '';
  testMessage.value = 'Essa é uma mensagem de teste!';
  emit('update:open', false);
};
</script>

<template>
  <DialogRoot :open="open" @update:open="emit('update:open', $event)">
    <DialogOverlay class="bg-blackA9 data-[state=open]:animate-overlayShow fixed inset-0 z-30" />
    <DialogContent
      class="data-[state=open]:animate-contentShow fixed top-[50%] left-[50%] max-h-[85vh] w-[90vw] max-w-[450px] translate-x-[-50%] translate-y-[-50%] rounded-[20px] bg-white p-[30px] shadow-[hsl(206_22%7%/_35%)_0px_10px_38px_-10px,_hsl(206_22%7%/_20%)_0px_10px_20px_-15px] focus:outline-none z-[100] font-sans"
    >
      <DialogTitle class="text-mauve12 m-0 text-[17px] font-semibold">
        Testar Conexão
      </DialogTitle>
      <DialogDescription class="text-mauve11 mt-[10px] mb-5 text-[15px] leading-normal">
        Insira o número de telefone e a mensagem para testar a conexão.
      </DialogDescription>

      <div class="flex flex-col gap-[10px]">
        <div class="flex flex-col">
          <label class="text-black font-semibold">Número</label>
          <div class="flex items-center h-[50px] border-[1.5px] border-gray-300 rounded-xl pl-[10px] transition-all duration-200 ease-in-out focus-within:border-[#2d79f3]">
            <Phone :size="18" color="#151717" class="dark:text-gray-400" />
            <input
              v-model="testPhoneNumber"
              v-mask="'(##) #####-####'"
              placeholder="(11) 99999-9999"
              class="ml-[10px] rounded-xl border-none w-full h-full bg-transparent text-black focus:outline-none placeholder:font-sans placeholder:text-gray-400 dark:placeholder:text-gray-500"
              required
            />
          </div>
        </div>
        <div class="flex flex-col">
          <label class="text-black font-semibold">Mensagem</label>
          <div class="flex items-start h-[100px] border-[1.5px] border-gray-300 rounded-xl pl-[10px] pt-[10px] transition-all duration-200 ease-in-out focus-within:border-[#2d79f3]">
            <MessageSquare :size="18" color="#151717" class="dark:text-gray-400" />
            <textarea
              v-model="testMessage"
              placeholder="Digite sua mensagem"
              class="ml-[10px] rounded-xl border-none w-full h-full bg-transparent text-black focus:outline-none placeholder:font-sans placeholder:text-gray-400 dark:placeholder:text-gray-500 resize-none"
              rows="3"
            ></textarea>
          </div>
        </div>
        <div class="flex justify-end gap-4 mt-[20px]">
          <DialogClose as-child>
            <button
              class="inline-flex items-center justify-center rounded-xl bg-gray-200 dark:bg-neutral-700 px-4 py-2 text-[15px] font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              @click="closeModal"
            >
              Cancelar
            </button>
          </DialogClose>
          <button
            @click="sendTestMessage"
            :disabled="isLoading"
            class="inline-flex items-center justify-center bg-black text-white text-[15px] font-medium rounded-xl h-[50px] w-full max-w-[150px] cursor-pointer hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            Enviar
          </button>
        </div>
      </div>

      <DialogClose
        class="text-black dark:text-gray-400 absolute top-[10px] right-[10px] inline-flex h-[25px] w-[25px] appearance-none items-center justify-center rounded-full focus:ring-2 focus:ring-[#2d79f3] focus:outline-none transition-colors"
        aria-label="Fechar"
        title="Fechar"
        @click="closeModal"
      >
        <X :size="20" />
      </DialogClose>
    </DialogContent>
  </DialogRoot>
</template>