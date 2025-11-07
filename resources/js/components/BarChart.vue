<script setup lang="ts">
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
import { defineProps } from 'vue';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps<{
    chartData: {
        labels: string[];
        datasets: {
            label: string;
            backgroundColor: string;
            data: number[];
        }[];
    };
}>();

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
};
</script>

<template>
    <Bar :data="chartData" :options="chartOptions" />
</template>
