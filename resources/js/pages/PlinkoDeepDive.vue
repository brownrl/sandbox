<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { defineProps, computed, ref, onMounted } from 'vue';
import BarChart from '@/components/BarChart.vue';
import HistogramChart from '@/components/HistogramChart.vue';

const props = defineProps<{
    statistics: {
        total_games: number;
        total_winnings: number;
        average_score: number;
        highest_score: number;
        landing_slot_distribution: Record<string, number>;
        average_horizontal_distance: number;
        horizontal_distance_histogram: Record<string, number>;
        average_fall_time_ms: number;
        average_peg_collisions: number;
        all_paths: { x: number; y: number }[][];
    };
}>();

const heatmapCanvas = ref<HTMLCanvasElement | null>(null);
const CANVAS_WIDTH = 600; // Match Plinko.vue
const CANVAS_HEIGHT = 700; // Match Plinko.vue

const landingSlotDistributionData = computed(() => {
    const labels = Object.keys(props.statistics.landing_slot_distribution);
    const data = Object.values(props.statistics.landing_slot_distribution);

    return {
        labels,
        datasets: [
            {
                label: 'Landing Slot Distribution',
                backgroundColor: '#a78bfa',
                data,
            },
        ],
    };
});

const horizontalDistanceHistogramData = computed(() => {
    const labels = Object.keys(props.statistics.horizontal_distance_histogram);
    const data = Object.values(props.statistics.horizontal_distance_histogram);

    return {
        labels,
        datasets: [
            {
                label: 'Horizontal Distance Distribution',
                backgroundColor: '#a78bfa',
                data,
            },
        ],
    };
});

const drawHeatmap = () => {

    if (!heatmapCanvas.value) return;



    const ctx = heatmapCanvas.value.getContext('2d');

    if (!ctx) return;



    ctx.clearRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);



    // Draw background gradient (matching Plinko.vue)

    const gradient = ctx.createLinearGradient(0, 0, 0, CANVAS_HEIGHT);

    gradient.addColorStop(0, '#1e1b4b');

    gradient.addColorStop(1, '#0f172a');

    ctx.fillStyle = gradient;

    ctx.fillRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);



    // Draw pegs (matching Plinko.vue)

    const ROWS = 12;

    const COLS = 9;

    const PEG_RADIUS = 5;

    const pegSpacingX = CANVAS_WIDTH / COLS; // Use COLS for spacing calculation

    const pegSpacingY = (CANVAS_HEIGHT - 150) / (ROWS + 1);



    const pegs = [];

    for (let row = 0; row < ROWS; row++) {

        const isEvenRow = row % 2 === 0;

        const pegsInRow = isEvenRow ? COLS : COLS - 1;

        const totalPegsWidth = (pegsInRow - 1) * pegSpacingX;

        const rowOffset = (CANVAS_WIDTH - totalPegsWidth) / 2;



        for (let col = 0; col < pegsInRow; col++) {

            pegs.push({

                x: rowOffset + pegSpacingX * col,

                y: 100 + pegSpacingY * row,

            });

        }

    }



    pegs.forEach(peg => {

        ctx.beginPath();

        ctx.arc(peg.x, peg.y, PEG_RADIUS, 0, Math.PI * 2);

        ctx.fillStyle = '#6d28d9';

        ctx.fill();

        ctx.strokeStyle = '#a78bfa';

        ctx.lineWidth = 2;

        ctx.stroke();

    });



    console.log('Heatmap data:', props.statistics.all_paths);



    // Draw heatmap points

    props.statistics.all_paths.forEach(point => {

        ctx.beginPath();

        ctx.arc(point.x, point.y, 2, 0, Math.PI * 2); // Smaller radius for heatmap points

        ctx.fillStyle = 'rgba(255, 255, 0, 0.1)'; // Semi-transparent yellow

        ctx.fill();

    });

};

onMounted(() => {
    drawHeatmap();
});
</script>

<template>
    <Head title="Plinko Deep Dive - Sandbox" />
    <div class="min-h-screen bg-gray-900 text-white p-8">
        <h1 class="text-4xl font-bold mb-8">Plinko Deep Dive</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-gray-800 p-6 rounded-lg">
                <h2 class="text-2xl font-bold mb-4">Total Games</h2>
                <p class="text-4xl">{{ statistics.total_games }}</p>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg">
                <h2 class="text-2xl font-bold mb-4">Total Winnings</h2>
                <p class="text-4xl">${{ statistics.total_winnings.toLocaleString() }}</p>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg">
                <h2 class="text-2xl font-bold mb-4">Average Score</h2>
                <p class="text-4xl">${{ statistics.average_score.toFixed(2) }}</p>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg">
                <h2 class="text-2xl font-bold mb-4">Highest Score</h2>
                <p class="text-4xl">${{ statistics.highest_score.toLocaleString() }}</p>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg">
                <h2 class="text-2xl font-bold mb-4">Average Horizontal Distance</h2>
                <p class="text-4xl">{{ statistics.average_horizontal_distance.toFixed(2) }}px</p>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg">
                <h2 class="text-2xl font-bold mb-4">Average Fall Time</h2>
                <p class="text-4xl">{{ statistics.average_fall_time_ms.toFixed(2) }}ms</p>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg">
                <h2 class="text-2xl font-bold mb-4">Average Peg Collisions</h2>
                <p class="text-4xl">{{ statistics.average_peg_collisions.toFixed(2) }}</p>
            </div>
        </div>
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div>
                <h2 class="text-3xl font-bold mb-4">Landing Slot Distribution</h2>
                <div class="bg-gray-800 p-6 rounded-lg">
                    <BarChart :chart-data="landingSlotDistributionData" />
                </div>
            </div>
            <div>
                <h2 class="text-3xl font-bold mb-4">Horizontal Distance Distribution</h2>
                <div class="bg-gray-800 p-6 rounded-lg">
                    <HistogramChart :chart-data="horizontalDistanceHistogramData" />
                </div>
            </div>
        </div>
        <div class="mt-8">
            <h2 class="text-3xl font-bold mb-4">Chip Path Heatmap</h2>
            <div class="bg-gray-800 p-6 rounded-lg flex justify-center">
                <canvas
                    ref="heatmapCanvas"
                    :width="CANVAS_WIDTH"
                    :height="CANVAS_HEIGHT"
                    class="rounded-lg shadow-2xl"
                ></canvas>
            </div>
        </div>
    </div>
</template>
