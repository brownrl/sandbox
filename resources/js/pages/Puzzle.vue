<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';

interface Tile {
    value: number;
    position: number;
}

const tiles = ref<Tile[]>([]);
const moves = ref(0);
const isWon = ref(false);
const showFireworks = ref(false);

// Initialize the puzzle in solved state
const initializePuzzle = () => {
    tiles.value = [];
    for (let i = 1; i <= 15; i++) {
        tiles.value.push({ value: i, position: i - 1 });
    }
    // Position 15 is empty (bottom-right)
    moves.value = 0;
    isWon.value = false;
    showFireworks.value = false;
};

// Get the empty position
const emptyPosition = computed(() => {
    return tiles.value.length < 15 ? 15 : 
        Array.from({ length: 16 }, (_, i) => i).find(
            pos => !tiles.value.some(tile => tile.position === pos)
        ) ?? 15;
});

// Check if a tile can move to the empty space
const canMove = (position: number): boolean => {
    const empty = emptyPosition.value;
    const row = Math.floor(position / 4);
    const col = position % 4;
    const emptyRow = Math.floor(empty / 4);
    const emptyCol = empty % 4;

    // Check if adjacent (same row and adjacent column, or same column and adjacent row)
    return (row === emptyRow && Math.abs(col - emptyCol) === 1) ||
           (col === emptyCol && Math.abs(row - emptyRow) === 1);
};

// Move a tile
const moveTile = (tile: Tile) => {
    if (!canMove(tile.position) || isWon.value) {
        return;
    }

    tile.position = emptyPosition.value;
    moves.value++;

    // Check if won
    checkWin();
};

// Check if puzzle is solved
const checkWin = () => {
    // Check if each tile's value matches its position + 1
    // Tile with value 1 should be at position 0, value 2 at position 1, etc.
    const isSolved = tiles.value.every(tile => tile.value === tile.position + 1);
    if (isSolved) {
        isWon.value = true;
        showFireworks.value = true;
        setTimeout(() => {
            showFireworks.value = false;
        }, 5000);
    }
};

// Scramble the puzzle with solvable configuration
const scramblePuzzle = () => {
    // Start with solved state
    const positions = Array.from({ length: 15 }, (_, i) => i);
    
    // Fisher-Yates shuffle
    for (let i = positions.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [positions[i], positions[j]] = [positions[j], positions[i]];
    }

    // Check if solvable, if not swap first two tiles
    if (!isSolvable(positions)) {
        [positions[0], positions[1]] = [positions[1], positions[0]];
    }

    // Assign positions to tiles
    tiles.value.forEach((tile, index) => {
        tile.position = positions[index];
    });

    moves.value = 0;
    isWon.value = false;
    showFireworks.value = false;
};

// Check if a configuration is solvable
// For 15-puzzle: count inversions, if even then solvable
const isSolvable = (positions: number[]): boolean => {
    let inversions = 0;
    const values = positions.map((pos, idx) => idx + 1);
    
    for (let i = 0; i < values.length - 1; i++) {
        for (let j = i + 1; j < values.length; j++) {
            const posI = positions[i];
            const posJ = positions[j];
            if (posI > posJ) {
                inversions++;
            }
        }
    }

    // For 15-puzzle with blank in bottom-right, even inversions = solvable
    return inversions % 2 === 0;
};

// Get tile at position
const getTileAt = (position: number) => {
    return tiles.value.find(tile => tile.position === position);
};

onMounted(() => {
    initializePuzzle();
});
</script>

<template>
    <Head title="Puzzle - Sandbox" />
    
    <div class="min-h-screen bg-gradient-to-br from-red-50 via-pink-50 to-red-100 relative overflow-hidden">
        <!-- Japanese cherry blossom decorations -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-10 left-10 text-pink-300 opacity-30 text-9xl">🌸</div>
            <div class="absolute top-40 right-20 text-pink-400 opacity-20 text-7xl">🌸</div>
            <div class="absolute bottom-20 left-1/4 text-pink-300 opacity-25 text-8xl">🌸</div>
            <div class="absolute bottom-40 right-1/3 text-pink-400 opacity-30 text-6xl">🌸</div>
        </div>

        <div class="container mx-auto px-4 py-12 relative z-10">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-5xl font-bold text-red-900 mb-4 font-serif">
                    数字パズル
                </h1>
                <p class="text-xl text-red-700 font-serif italic">15 Puzzle Game</p>
            </div>

            <!-- Game Container -->
            <div class="max-w-2xl mx-auto">
                <!-- Stats and Controls -->
                <div class="bg-white/80 backdrop-blur-sm rounded-lg shadow-lg p-6 mb-6 border-4 border-red-900">
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-center flex-1">
                            <p class="text-sm text-red-700 font-serif mb-1">Moves</p>
                            <p class="text-4xl font-bold text-red-900">{{ moves }}</p>
                        </div>
                        <button
                            @click="scramblePuzzle"
                            class="px-8 py-4 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 active:scale-95 font-serif border-2 border-red-800"
                        >
                            🎲 Scramble
                        </button>
                    </div>
                    
                    <div v-if="isWon" class="mt-4 p-4 bg-gradient-to-r from-yellow-100 to-pink-100 border-2 border-yellow-600 rounded-lg">
                        <p class="text-2xl font-bold text-center text-red-900 font-serif">
                            🎉 おめでとう！ Congratulations! 🎉
                        </p>
                        <p class="text-center text-red-700 mt-2 font-serif">
                            Solved in <span class="font-bold">{{ moves }}</span> moves!
                        </p>
                    </div>
                </div>

                <!-- Puzzle Grid -->
                <div class="bg-gradient-to-br from-red-900 to-pink-900 p-6 rounded-2xl shadow-2xl border-4 border-red-950">
                    <div class="grid grid-cols-4 gap-2 bg-red-950/30 p-4 rounded-xl">
                        <template v-for="position in 16" :key="position">
                            <div
                                v-if="getTileAt(position - 1)"
                                @click="moveTile(getTileAt(position - 1)!)"
                                :class="[
                                    'aspect-square rounded-lg flex items-center justify-center text-4xl font-bold transition-all duration-200 font-serif',
                                    canMove(position - 1) && !isWon
                                        ? 'bg-gradient-to-br from-white to-pink-50 text-red-900 cursor-pointer hover:scale-105 hover:shadow-xl shadow-lg border-4 border-red-800'
                                        : 'bg-gradient-to-br from-gray-200 to-gray-300 text-gray-600 cursor-not-allowed border-4 border-gray-500'
                                ]"
                            >
                                {{ getTileAt(position - 1)!.value }}
                            </div>
                            <div
                                v-else
                                class="aspect-square rounded-lg bg-red-950/50 border-2 border-red-900/30"
                            />
                        </template>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mt-6 bg-white/80 backdrop-blur-sm rounded-lg shadow-lg p-6 border-2 border-red-800">
                    <h3 class="text-lg font-bold text-red-900 mb-3 font-serif">How to Play:</h3>
                    <ul class="text-red-800 space-y-2 font-serif">
                        <li class="flex items-start">
                            <span class="mr-2">🎯</span>
                            <span>Click on a tile adjacent to the empty space to move it</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">🎲</span>
                            <span>Click "Scramble" to randomize the puzzle</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-2">🏆</span>
                            <span>Arrange tiles in order from 1 to 15 to win!</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Fireworks Animation -->
        <Transition
            enter-active-class="transition-opacity duration-500"
            leave-active-class="transition-opacity duration-1000"
            enter-from-class="opacity-0"
            leave-to-class="opacity-0"
        >
            <div v-if="showFireworks" class="fixed inset-0 pointer-events-none z-50">
                <!-- Multiple firework bursts -->
                <div
                    v-for="n in 12"
                    :key="n"
                    :class="[
                        'absolute animate-ping',
                        n % 2 === 0 ? 'text-yellow-400' : 'text-pink-400',
                        n <= 4 ? 'text-8xl' : 'text-6xl'
                    ]"
                    :style="{
                        left: `${(n * 73 + 10) % 90}%`,
                        top: `${(n * 47 + 15) % 70}%`,
                        animationDelay: `${(n * 0.3) % 2}s`,
                        animationDuration: '2s'
                    }"
                >
                    {{ ['✨', '🎆', '💥', '⭐', '🌟'][n % 5] }}
                </div>
                
                <!-- Falling cherry blossoms -->
                <div
                    v-for="n in 20"
                    :key="`blossom-${n}`"
                    class="absolute text-pink-400 text-4xl animate-bounce"
                    :style="{
                        left: `${(n * 5) % 100}%`,
                        top: `${(n * 3) % 80}%`,
                        animationDelay: `${(n * 0.2) % 3}s`,
                        animationDuration: '1.5s'
                    }"
                >
                    🌸
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}
</style>
