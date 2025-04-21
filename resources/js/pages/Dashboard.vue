<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import VueApexCharts from 'vue3-apexcharts';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const message = ref('');
const totalConnections = ref(0);
const activeConnections = ref(0);
const inactiveConnections = ref(0);

const chartOptions = ref({
    chart: {
        type: 'pie',
        toolbar: {
            show: false
        }
    },
    colors: ['#28a745', '#dc3545'],
    labels: ['Conexões Ativas', 'Conexões Inativas'],
    legend: {
        position: 'bottom',
        horizontalAlign: 'center',
        floating: false,
        fontSize: '14px',
        fontFamily: 'Inter, sans-serif',
        fontWeight: 500,
        markers: {
            width: 12,
            height: 12,
            radius: 12
        }
    },
    plotOptions: {
        pie: {
            donut: {
                size: '70%',
                labels: {
                    show: false,
                    name: {
                        show: true,
                        fontSize: '14px',
                        fontFamily: 'Inter, sans-serif',
                        fontWeight: 500,
                        color: '#6B7280'
                    },
                    value: {
                        show: true,
                        fontSize: '20px',
                        fontFamily: 'Inter, sans-serif',
                        fontWeight: 600,
                        color: '#111827'
                    },
                    total: {
                        show: true,
                        showAlways: true,
                        label: 'Total',
                        fontSize: '14px',
                        fontFamily: 'Inter, sans-serif',
                        fontWeight: 500,
                        color: '#6B7280'
                    }
                }
            }
        }
    },
    dataLabels: {
        enabled: false
    },
    tooltip: {
        theme: 'dark',
        y: {
            formatter: function(val) {
                return val + ' conexões'
            }
        }
    }
});

const series = ref([0, 0]);

const fetchConnectionsData = async () => {
    try {
        const response = await axios.get('/connections/chart-data');
        if (response.data) {
            totalConnections.value = response.data.total;
            activeConnections.value = response.data.active;
            inactiveConnections.value = response.data.inactive;
            series.value = response.data.series;
        }
    } catch (error) {
        console.error('Erro ao buscar dados das conexões:', error);
    }
};

onMounted(() => {
    const channel = window.Echo.channel('dashboard-channel');
    channel.listen('.hello-world', (event: { message: string }) => {
        message.value = event.message;
        console.log(event);
    });

    fetchConnectionsData();
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative h-40 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
                    <div class="flex flex-col h-full">
                        <h3 class="text-lg font-semibold mb-2">Total de Conexões</h3>
                        <div class="flex-1 flex items-center justify-center">
                            <span class="text-4xl font-bold text-primary">{{ totalConnections }}</span>
                        </div>
                    </div>
                </div>
                <div class="relative h-40 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
                    <div class="flex flex-col h-full">
                        <h3 class="text-lg font-semibold mb-2">Conexões Ativas</h3>
                        <div class="flex-1 flex items-center justify-center">
                            <span class="text-4xl font-bold text-[#28a745]">{{ activeConnections }}</span>
                        </div>
                    </div>
                </div>
                <div class="relative h-40 overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
                    <div class="flex flex-col h-full">
                        <h3 class="text-lg font-semibold mb-2">Conexões Inativas</h3>
                        <div class="flex-1 flex items-center justify-center">
                            <span class="text-4xl font-bold text-[#dc3545]">{{ inactiveConnections }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
                <h3 class="text-lg font-semibold mb-4">Evolução das Conexões</h3>
                <div class="h-[400px]">
                    <VueApexCharts
                        type="pie"
                        height="100%"
                        :options="chartOptions"
                        :series="series"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style>
.apexcharts-legend-marker {
    margin-right: 3px !important;
}
</style>