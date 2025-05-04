<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData } from '@/types';
import axios from 'axios';
import { useToast } from 'vue-toastification';
import CopyTokenButton from '@/components/connection-list/copy-token-button/CopyTokenButton.vue';
import { ClipboardCopy } from 'lucide-vue-next';
import { watchEffect } from 'vue';

const props = defineProps<{ api_key?: string }>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Configurações de integração',
        href: '/settings/integration',
    },
];

const page = usePage<SharedData>();
const organization = page.props.auth.organization;

const form = useForm({
    api_key: props.api_key || '',
});

watchEffect(() => {
    if (props.api_key && !form.api_key) {
        form.api_key = props.api_key;
    }
});

const toast = useToast();

const generateApiKey = async () => {
    try {
        const response = await axios.post(route('settings.integration.generate'), {}, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        });
        form.api_key = response.data.api_key;
        toast.success(response.data.success || 'Chave de API gerada com sucesso!');
    } catch (error) {
        toast.error('Erro ao gerar chave de API');
    }
};

const copyApiKey = async () => {
    try {
        await navigator.clipboard.writeText(form.api_key);
        toast.success('Chave copiada!');
    } catch (err) {
        toast.error('Falha ao copiar a chave.');
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Configurações de integração" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Configurações de integração"
                    description="Gerencie sua chave de API para integrações"
                />

                <div class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="api_key">Chave de API</Label>
                        <div class="flex gap-2 items-center">
                            <Input
                                id="api_key"
                                class="mt-0 h-10 block w-full"
                                v-model="form.api_key"
                                readonly
                                placeholder="Clique em gerar para criar uma nova chave"
                            />
                            <template v-if="form.api_key">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center h-10 px-3 rounded-md border border-input bg-background text-sm font-medium shadow-sm transition-colors hover:bg-muted focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                    @click="copyApiKey"
                                >
                                    <ClipboardCopy :size="18" class="mr-1" />
                                    Copiar
                                </button>
                            </template>
                            <template v-else>
                                <Button type="button" @click="generateApiKey" :disabled="form.processing" class="h-10">
                                    Gerar
                                </Button>
                            </template>
                        </div>
                        <InputError class="mt-2" :message="form.errors.api_key" />
                    </div>

                    <div class="rounded-md bg-muted p-4">
                        <h3 class="text-sm font-medium">Como usar sua chave de API</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Use sua chave de API para autenticar requisições à nossa API. Inclua a chave no header
                            <code class="rounded bg-muted px-1 py-0.5">Authorization Bearer</code> de suas requisições.
                        </p>
                    </div>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template> 