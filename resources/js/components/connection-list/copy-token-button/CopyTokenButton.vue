<!-- resources/js/components/connection-list/copy-token-button/CopyTokenButton.vue -->
<script setup lang="ts">
import { DropdownMenuItem } from 'radix-vue';
import { ClipboardCopy } from 'lucide-vue-next';
import { useToast } from 'vue-toastification';

const props = defineProps<{
  token: string;
}>();

const toast = useToast();

const copyToken = async () => {
  try {
    await navigator.clipboard.writeText(props.token); // 'token' agora é reconhecido como prop
    toast.success('Token copiado!');
  } catch (err) {
    console.error('Failed to copy token:', err);
    toast.error('Falha ao copiar o token.');
  }
};
</script>

<template>
  <DropdownMenuItem
    class="w-full px-4 py-2 text-sm text-neutral-800 dark:text-neutral-200 hover:bg-neutral-100 dark:hover:bg-neutral-700 flex items-center gap-2 cursor-pointer"
    @click="copyToken"
  >
    <ClipboardCopy :size="16" />
    Copiar token
  </DropdownMenuItem>
</template>