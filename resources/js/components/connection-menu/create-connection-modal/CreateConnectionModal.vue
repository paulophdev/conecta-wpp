<!-- @/components/modals/CreateConnectionModal.vue -->
<script setup lang="ts">
import {
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogOverlay,
  DialogPortal,
  DialogRoot,
  DialogTitle,
  DialogTrigger,
} from 'radix-vue';
import { X, ALargeSmall, Link2 } from 'lucide-vue-next';
import { ref } from 'vue';

const emit = defineEmits(['create']);

const name = ref('');
const webhookUrl = ref(`${import.meta.env.VITE_APP_URL}/webhook/default`);
const isActive = ref(true);

const resetForm = () => {
  name.value = '';
  webhookUrl.value = `${import.meta.env.VITE_APP_URL}/webhook/default`;
  isActive.value = true;
};

const submit = (event: Event) => {
  event.preventDefault();
  const connectionData = {
    name: name.value,
    webhook_url: webhookUrl.value,
    is_active: isActive.value,
  };
  emit('create', connectionData);
  resetForm();
};
</script>

<template>
  <DialogRoot>
    <DialogTrigger as-child>
      <slot name="trigger" />
    </DialogTrigger>

    <DialogPortal>
      <DialogOverlay class="bg-blackA9 data-[state=open]:animate-overlayShow fixed inset-0 z-30" />
      <DialogContent
        class="data-[state=open]:animate-contentShow fixed top-[50%] left-[50%] max-h-[85vh] w-[90vw] max-w-[450px] translate-x-[-50%] translate-y-[-50%] rounded-[20px] bg-white p-[30px] shadow-[hsl(206_22%7%/_35%)_0px_10px_38px_-10px,_hsl(206_22%7%/_20%)_0px_10px_20px_-15px] focus:outline-none z-[100] font-sans"
      >
        <DialogTitle class="text-mauve12 m-0 text-[17px] font-semibold">
          Criar Nova Conexão
        </DialogTitle>
        <DialogDescription class="text-mauve11 mt-[10px] mb-5 text-[15px] leading-normal">
          Insira os detalhes da nova conexão. Clique em salvar quando terminar.
        </DialogDescription>

        <form @submit="submit" class="flex flex-col gap-[10px]">
          <!-- Campo Nome -->
          <div class="flex flex-col">
            <label class="text-black font-semibold">Nome</label>
            <div
              class="flex items-center h-[50px] border-[1.5px] border-gray-300 rounded-xl pl-[10px] transition-all duration-200 ease-in-out focus-within:border-[#2d79f3]"
            >
            <ALargeSmall :size="18" color="#151717" class="dark:text-gray-400" />
            <input
            v-model="name"
            placeholder="Nome da conexão"
            class="ml-[10px] rounded-xl border-none w-full h-full bg-transparent text-black focus:outline-none placeholder:font-sans placeholder:text-gray-400 dark:placeholder:text-gray-500"
            required
          />
            </div>
          </div>

          <!-- Campo Webhook URL -->
          <div class="flex flex-col">
            <label class="text-black font-semibold">Webhook URL</label>
            <div
              class="flex items-center h-[50px] border-[1.5px] border-gray-300 rounded-xl pl-[10px] transition-all duration-200 ease-in-out focus-within:border-[#2d79f3]"
            >
                <Link2 :size="18" color="#151717" class="dark:text-gray-400" />
                <input
                v-model="webhookUrl"
                type="url"
                placeholder="https://example.com/webhook"
                class="ml-[10px] rounded-xl border-none w-full h-full bg-transparent text-black focus:outline-none placeholder:font-sans placeholder:text-gray-400 dark:placeholder:text-gray-500"
                required
              />
            </div>
          </div>

          <!-- Botão Salvar -->
          <button
            type="submit"
            class="mt-[20px] mb-[10px] bg-black text-white text-[15px] font-medium rounded-xl h-[50px] w-full cursor-pointer"
          >
            Salvar
          </button>
        </form>

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