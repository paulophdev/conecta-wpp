<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { CardActions } from '@/components/ui/card-actions';

defineProps<{
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

</script>

<template>
    <Head title="Conexões" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4 h-full p-4">
            <template v-for="connection in connections" :key="connection.id">
                <CardActions v-bind="connection" />
            </template>
        </div>
    </AppLayout>
</template>