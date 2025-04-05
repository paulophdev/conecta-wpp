<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { ref } from 'vue';
import { Search } from 'lucide-vue-next'

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();

const searchQuery = ref('');

const emit = defineEmits(['update:search']);

const handleInput = (event: Event) => {
  const target = event.target as HTMLInputElement;
  searchQuery.value = target.value;
  emit('update:search', searchQuery.value);
};
</script>

<template>
    <div class="flex items-center relative w-full min-w-[300px] max-w-[600px] group">
        <Search class="absolute left-4 w-4 h-4 text-gray-500 dark:text-[#bdbecb] pointer-events-none z-[1]" />

        <input
            id="query"
            class="w-full h-[45px] pl-10 pr-4 border-2 border-gray-200 dark:border-[#2b2c37] rounded-xl bg-white dark:bg-[#16171d] outline-none text-gray-900 dark:text-[#bdbecb] transition-all duration-250 ease-[cubic-bezier(0.19,1,0.22,1)] cursor-text z-0 placeholder:text-gray-500 dark:placeholder:text-[#bdbecb] hover:border-gray-300 dark:hover:border-[#2f303d] active:scale-95 focus:border-gray-300 dark:focus:border-[#2f303d] [input::-webkit-search-cancel-button]:appearance-none [input::-webkit-search-cancel-button]:w-3 [input::-webkit-search-cancel-button]:h-3 [input::-webkit-search-cancel-button]:mr-2"
            type="search"
            placeholder="Search..."
            name="searchbar"
            v-model="searchQuery"
            @input="handleInput"
        />
    </div>
</template>