<!-- resources/js/components/modals/CreateConnectionModal.vue -->
<script setup lang="ts">
import { DialogRoot, DialogContent, DialogTitle, DialogDescription, DialogClose, DialogTrigger, DialogPortal, DialogOverlay } from 'radix-vue';
import { X, ALargeSmall, Link2 } from 'lucide-vue-next';
import { ref, onMounted, onUpdated } from 'vue';

const props = defineProps<{
  connection?: { id?: number; name: string; webhook_url: string; is_active: boolean };
}>();

const name = ref('');
const webhookUrl = ref(`${import.meta.env.VITE_APP_URL}/webhook/default`);
const isActive = ref(true);
const isOpen = ref(false);

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
};

const openModal = () => {
  isOpen.value = true;
};

const closeModal = () => {
  isOpen.value = false;
  resetForm();
};

defineExpose({ openModal, closeModal });

onMounted(() => {
  if (props.connection) {
    name.value = props.connection.name;
    webhookUrl.value = props.connection.webhook_url;
  }
});

onUpdated(() => {
  if (props.connection) {
    name.value = props.connection.name;
    webhookUrl.value = props.connection.webhook_url;
  }
});

const emit = defineEmits(['create']);
</script>

<template>
  <DialogRoot v-model:open="isOpen">
    <DialogTrigger as-child>
      <slot name="trigger" />
    </DialogTrigger>
    <DialogPortal>
      <DialogOverlay class="bg-blackA9 data-[state=open]:animate-overlayShow fixed inset-0 z-30" />
      <DialogContent
        class="data-[state=open]:animate-contentShow fixed top-[50%] left-[50%] max-h-[85vh] w-[90vw] max-w-[450px] translate-x-[-50%] translate-y-[-50%] rounded-[20px] bg-white p-[30px] shadow-[hsl(206_22%7%/_35%)_0px_10px_38px_-10px,_hsl(206_22%7%/_20%)_0px_10px_20px_-15px] focus:outline-none z-[100] font-sans"
      >
        <DialogTitle class="text-mauve12 m-0 text-[17px] font-semibold">
          {{ connection ? 'Editar Conexão' : 'Criar Nova Conexão' }}
        </DialogTitle>
        <DialogDescription class="text-mauve11 mt-[10px] mb-5 text-[15px] leading-normal">
          {{ connection ? 'Edite os dados da conexão.' : 'Insira os detalhes da nova conexão.' }}
        </DialogDescription>

        <form @submit="submit" class="flex flex-col gap-[10px]">
          <div class="flex flex-col">
            <label class="text-black font-semibold">Nome</label>
            <div class="flex items-center h-[50px] border-[1.5px] border-gray-300 rounded-xl pl-[10px] transition-all duration-200 ease-in-out focus-within:border-[#2d79f3]">
              <ALargeSmall :size="18" color="#151717" class="dark:text-gray-400" />
              <input
                v-model="name"
                placeholder="Nome da conexão"
                class="ml-[10px] rounded-xl border-none w-full h-full bg-transparent text-black focus:outline-none placeholder:font-sans placeholder:text-gray-400 dark:placeholder:text-gray-500"
                required
              />
            </div>
          </div>
          <div class="flex flex-col">
            <label class="text-black font-semibold">Webhook URL</label>
            <div class="flex items-center h-[50px] border-[1.5px] border-gray-300 rounded-xl pl-[10px] transition-all duration-200 ease-in-out focus-within:border-[#2d79f3]">
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
          <button
            type="submit"
            class="mt-[20px] mb-[10px] bg-black text-white text-[15px] font-medium rounded-xl h-[50px] w-full cursor-pointer"
          >
            {{ connection ? 'Salvar' : 'Salvar' }}
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