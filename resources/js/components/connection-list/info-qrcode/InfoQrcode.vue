<!-- resources/js/components/connection-list/info-qrcode/InfoQrcode.vue -->
<script setup lang="ts">
import { ListTodo, QrCode } from 'lucide-vue-next';
import type { HTMLAttributes } from 'vue';

interface Props {
  is_active?: boolean;
  class?: HTMLAttributes['class'];
}

const props = withDefaults(defineProps<Props>(), {
  is_active: false,
});

const emit = defineEmits(['open-modal']);

const handleClick = () => {
  emit('open-modal');
};
</script>

<template>
  <button
    class="group/btn relative flex-1 overflow-hidden rounded-xl bg-indigo-500 p-px font-medium text-white"
    :class="props.class"
    @click="handleClick"
  >
    <div
      class="relative rounded-xl bg-slate-500/50 px-4 py-3 transition-colors group-hover/btn:bg-transparent"
    >
      <span class="relative flex items-center justify-center gap-2">
        <ListTodo size="17" v-if="is_active" />
        <QrCode size="17" v-else />
        <span
          class="transition-transform duration-300 group-hover/btn:translate-x-1"
          v-text="is_active ? 'Detalhes' : 'QR Code'"
        ></span>
      </span>
    </div>
  </button>
</template>