<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Card } from '@/components/ui/card';

interface Game {
    id: number;
    user_id: number | null;
    user?: { id: number; name: string };
    score: number;
    drop_position: number;
    final_slot: number;
    created_at: string;
}

interface Statistics {
    total_games: number;
    total_winnings: number;
    average_score: number;
    highest_score: number;
}

const props = defineProps<{
    recentGames: Game[];
    highScores: Game[];
    statistics: Statistics;
}>();

// Game state
const canvas = ref<HTMLCanvasElement | null>(null);
const isDropping = ref(false);
const currentScore = ref<number | null>(null);
const showScore = ref(false);

// Physics constants
const ROWS = 12;
const COLS = 9;
const PEG_RADIUS = 5;
const CHIP_RADIUS = 10;
const GRAVITY = 0.6;
const BOUNCE = 0.6;
const HORIZONTAL_BOUNCE = 0.8;

// Slot prizes (from center outward)
const SLOT_PRIZES = [100, 500, 1000, 0, 10000, 0, 1000, 500, 100];

// Canvas dimensions
const CANVAS_WIDTH = 600;
const CANVAS_HEIGHT = 700;

// Chip state
interface Chip {
    x: number;
    y: number;
    vx: number;
    vy: number;
    radius: number;
}

let chip: Chip | null = null;
let ctx: CanvasRenderingContext2D | null = null;
let animationFrameId: number | null = null;
let pegs: { x: number; y: number }[] = [];
let slots: { x: number; prize: number }[] = [];

const initCanvas = () => {
    if (!canvas.value) return;
    
    ctx = canvas.value.getContext('2d');
    if (!ctx) return;

    // Calculate peg positions
    pegs = [];
    const pegSpacingX = CANVAS_WIDTH / COLS;
    const pegSpacingY = (CANVAS_HEIGHT - 150) / (ROWS + 1);

    for (let row = 0; row < ROWS; row++) {
        // Alternate between full row and offset row
        const pegsInRow = COLS + 1;
        const offset = row % 2 === 0 ? 0 : pegSpacingX / 2;
        
        for (let col = 0; col < pegsInRow; col++) {
            pegs.push({
                x: pegSpacingX * col + offset,
                y: 100 + pegSpacingY * row,
            });
        }
    }

    // Calculate slot positions
    slots = [];
    const slotSpacing = CANVAS_WIDTH / COLS;
    for (let i = 0; i < COLS; i++) {
        slots.push({
            x: slotSpacing * i + slotSpacing / 2,
            prize: SLOT_PRIZES[i],
        });
    }

    drawBoard();
};

const drawBoard = () => {
    if (!ctx) return;

    // Clear canvas
    ctx.clearRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);

    // Draw background gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, CANVAS_HEIGHT);
    gradient.addColorStop(0, '#1e1b4b');
    gradient.addColorStop(1, '#0f172a');
    ctx.fillStyle = gradient;
    ctx.fillRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);

    // Draw pegs
    pegs.forEach(peg => {
        ctx!.beginPath();
        ctx!.arc(peg.x, peg.y, PEG_RADIUS, 0, Math.PI * 2);
        ctx!.fillStyle = '#6d28d9';
        ctx!.fill();
        ctx!.strokeStyle = '#a78bfa';
        ctx!.lineWidth = 2;
        ctx!.stroke();
    });

    // Draw slots
    const slotSpacing = CANVAS_WIDTH / COLS;
    const slotY = CANVAS_HEIGHT - 80;
    
    slots.forEach((slot, index) => {
        const x = index * slotSpacing;
        
        // Slot background
        const isCenter = index === 4;
        ctx!.fillStyle = isCenter ? '#dc2626' : slot.prize === 0 ? '#374151' : '#059669';
        ctx!.fillRect(x, slotY, slotSpacing - 2, 60);

        // Slot border
        ctx!.strokeStyle = isCenter ? '#fca5a5' : slot.prize === 0 ? '#6b7280' : '#34d399';
        ctx!.lineWidth = 2;
        ctx!.strokeRect(x, slotY, slotSpacing - 2, 60);

        // Prize text
        ctx!.fillStyle = '#ffffff';
        ctx!.font = 'bold 14px sans-serif';
        ctx!.textAlign = 'center';
        ctx!.fillText(`$${slot.prize.toLocaleString()}`, x + slotSpacing / 2, slotY + 35);
    });

    // Draw drop zone indicators
    const dropZoneY = 40;
    for (let i = 0; i < COLS; i++) {
        const x = (CANVAS_WIDTH / COLS) * i + (CANVAS_WIDTH / COLS) / 2;
        ctx!.beginPath();
        ctx!.arc(x, dropZoneY, 8, 0, Math.PI * 2);
        ctx!.fillStyle = 'rgba(168, 85, 247, 0.3)';
        ctx!.fill();
        ctx!.strokeStyle = '#a855f7';
        ctx!.lineWidth = 1;
        ctx!.stroke();
    }

    // Draw chip if exists
    if (chip) {
        ctx!.beginPath();
        ctx!.arc(chip.x, chip.y, chip.radius, 0, Math.PI * 2);
        const chipGradient = ctx!.createRadialGradient(chip.x, chip.y, 0, chip.x, chip.y, chip.radius);
        chipGradient.addColorStop(0, '#fbbf24');
        chipGradient.addColorStop(1, '#f59e0b');
        ctx!.fillStyle = chipGradient;
        ctx!.fill();
        ctx!.strokeStyle = '#fef3c7';
        ctx!.lineWidth = 2;
        ctx!.stroke();
    }
};

const dropChip = (dropPosition: number) => {
    if (isDropping.value) return;
    
    isDropping.value = true;
    showScore.value = false;
    currentScore.value = null;

    const x = (CANVAS_WIDTH / COLS) * dropPosition + (CANVAS_WIDTH / COLS) / 2;
    chip = {
        x,
        y: 40,
        vx: (Math.random() - 0.5) * 3, // Increased initial randomness
        vy: 0,
        radius: CHIP_RADIUS,
    };

    animate(dropPosition);
};

const animate = (dropPosition: number) => {
    if (!chip || !ctx) return;

    // Apply gravity
    chip.vy += GRAVITY;
    chip.x += chip.vx;
    chip.y += chip.vy;

    // Check collision with pegs
    pegs.forEach(peg => {
        const dx = chip!.x - peg.x;
        const dy = chip!.y - peg.y;
        const distance = Math.sqrt(dx * dx + dy * dy);

        if (distance < chip!.radius + PEG_RADIUS) {
            // Calculate bounce angle
            const angle = Math.atan2(dy, dx);
            const speed = Math.sqrt(chip!.vx * chip!.vx + chip!.vy * chip!.vy);
            
            chip!.vx = Math.cos(angle) * speed * BOUNCE + (Math.random() - 0.5) * HORIZONTAL_BOUNCE;
            chip!.vy = Math.sin(angle) * speed * BOUNCE;

            // Move chip away from peg
            const overlap = chip!.radius + PEG_RADIUS - distance;
            chip!.x += Math.cos(angle) * overlap;
            chip!.y += Math.sin(angle) * overlap;
        }
    });

    // Wall collision
    if (chip.x - chip.radius < 0) {
        chip.x = chip.radius;
        chip.vx = -chip.vx * BOUNCE;
    }
    if (chip.x + chip.radius > CANVAS_WIDTH) {
        chip.x = CANVAS_WIDTH - chip.radius;
        chip.vx = -chip.vx * BOUNCE;
    }

    drawBoard();

    // Check if chip reached bottom
    if (chip.y >= CANVAS_HEIGHT - 100) {
        const slotSpacing = CANVAS_WIDTH / COLS;
        const finalSlot = Math.floor(chip.x / slotSpacing);
        const clampedSlot = Math.max(0, Math.min(COLS - 1, finalSlot));
        
        chip = null;
        isDropping.value = false;
        currentScore.value = SLOT_PRIZES[clampedSlot];
        showScore.value = true;

        // Save game result
        saveGame(dropPosition, clampedSlot, currentScore.value);

        return;
    }

    animationFrameId = requestAnimationFrame(() => animate(dropPosition));
};

const saveGame = async (dropPosition: number, finalSlot: number, score: number) => {
    try {
        await router.post('/plinko', {
            score,
            drop_position: dropPosition,
            final_slot: finalSlot,
        }, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                // Optionally reload to get updated stats
                setTimeout(() => {
                    router.reload({ only: ['recentGames', 'highScores', 'statistics'] });
                }, 2000);
            },
        });
    } catch (error) {
        console.error('Failed to save game:', error);
    }
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formattedStats = computed(() => ({
    totalGames: props.statistics.total_games.toLocaleString(),
    totalWinnings: `$${Math.round(props.statistics.total_winnings).toLocaleString()}`,
    averageScore: `$${Math.round(props.statistics.average_score).toLocaleString()}`,
    highestScore: `$${Math.round(props.statistics.highest_score).toLocaleString()}`,
}));

onMounted(() => {
    initCanvas();
});

onUnmounted(() => {
    if (animationFrameId) {
        cancelAnimationFrame(animationFrameId);
    }
});
</script>

<template>
    <Head title="Plinko - Sandbox" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 dark:from-black dark:via-purple-950 dark:to-black p-8">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25px 25px, rgba(255,255,255,0.1) 2px, transparent 0); background-size: 50px 50px;"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-6xl font-bold text-white mb-2">Plinko</h1>
                <p class="text-xl text-purple-200">Drop the chip and win big!</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Game Area -->
                <div class="lg:col-span-2">
                    <Card class="bg-slate-800/50 backdrop-blur-sm border-purple-500/30 p-6">
                        <div class="flex flex-col items-center">
                            <!-- Canvas -->
                            <canvas
                                ref="canvas"
                                :width="CANVAS_WIDTH"
                                :height="CANVAS_HEIGHT"
                                class="rounded-lg shadow-2xl mb-6"
                            ></canvas>

                            <!-- Drop Buttons -->
                            <div class="flex gap-2 mb-4">
                                <button
                                    v-for="i in COLS"
                                    :key="i"
                                    @click="dropChip(i - 1)"
                                    :disabled="isDropping"
                                    class="px-4 py-2 bg-purple-600 hover:bg-purple-500 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition-colors"
                                >
                                    {{ i }}
                                </button>
                            </div>

                            <!-- Score Display -->
                            <div v-if="showScore" class="text-center">
                                <div class="text-5xl font-bold text-yellow-400 mb-2 animate-pulse">
                                    ${{ currentScore?.toLocaleString() }}
                                </div>
                                <div v-if="currentScore === 10000" class="text-2xl text-green-400 font-bold">
                                    🎉 JACKPOT! 🎉
                                </div>
                                <div v-else-if="currentScore === 0" class="text-xl text-red-400">
                                    Better luck next time!
                                </div>
                                <div v-else class="text-xl text-purple-200">
                                    Nice win!
                                </div>
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Statistics -->
                    <Card class="bg-slate-800/50 backdrop-blur-sm border-purple-500/30 p-6">
                        <h2 class="text-2xl font-bold text-white mb-4">Statistics</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-purple-200">Total Games:</span>
                                <span class="text-white font-bold">{{ formattedStats.totalGames }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-purple-200">Total Winnings:</span>
                                <span class="text-green-400 font-bold">{{ formattedStats.totalWinnings }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-purple-200">Average Score:</span>
                                <span class="text-blue-400 font-bold">{{ formattedStats.averageScore }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-purple-200">High Score:</span>
                                <span class="text-yellow-400 font-bold">{{ formattedStats.highestScore }}</span>
                            </div>
                        </div>
                    </Card>

                    <!-- High Scores -->
                    <Card class="bg-slate-800/50 backdrop-blur-sm border-purple-500/30 p-6">
                        <h2 class="text-2xl font-bold text-white mb-4">High Scores</h2>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            <div
                                v-for="(game, index) in highScores"
                                :key="game.id"
                                class="flex justify-between items-center p-2 rounded bg-slate-700/50"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="text-purple-400 font-bold">{{ index + 1 }}.</span>
                                    <span class="text-white text-sm">{{ game.user?.name || 'Guest' }}</span>
                                </div>
                                <span class="text-yellow-400 font-bold">${{ game.score.toLocaleString() }}</span>
                            </div>
                        </div>
                    </Card>

                    <!-- Recent Games -->
                    <Card class="bg-slate-800/50 backdrop-blur-sm border-purple-500/30 p-6">
                        <h2 class="text-2xl font-bold text-white mb-4">Recent Games</h2>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            <div
                                v-for="game in recentGames"
                                :key="game.id"
                                class="p-2 rounded bg-slate-700/50"
                            >
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-white text-sm">{{ game.user?.name || 'Guest' }}</span>
                                    <span class="text-yellow-400 font-bold text-sm">${{ game.score.toLocaleString() }}</span>
                                </div>
                                <div class="text-purple-300 text-xs">{{ formatDate(game.created_at) }}</div>
                            </div>
                        </div>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
canvas {
    border: 2px solid rgba(168, 85, 247, 0.5);
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
