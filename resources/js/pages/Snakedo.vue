<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';

interface Cell {
    row: number;
    col: number;
    number?: number; // 1-8 for collectible numbers
    isSnakeHead: boolean;
    isSnakeBody: boolean;
}

interface Position {
    row: number;
    col: number;
}

const GRID_SIZE = 9;
const grid = ref<Cell[][]>([]);
const snakeHead = ref<Position>({ row: 0, col: 0 });
const snakeBody = ref<Position[]>([]); // Trail of visited positions
const currentTarget = ref(1); // Next number to collect (1-8)
const gameState = ref<'playing' | 'won' | 'lost'>('playing');
const moves = ref(0);

// Initialize empty grid
const initializeGrid = () => {
    grid.value = [];
    for (let row = 0; row < GRID_SIZE; row++) {
        const rowCells: Cell[] = [];
        for (let col = 0; col < GRID_SIZE; col++) {
            rowCells.push({
                row,
                col,
                isSnakeHead: false,
                isSnakeBody: false
            });
        }
        grid.value.push(rowCells);
    }
};

// Generate a solvable board
const generateSolvableBoard = () => {
    initializeGrid();
    snakeBody.value = [];
    currentTarget.value = 1;
    gameState.value = 'playing';
    moves.value = 0;

    // Step 1: Generate a valid path that visits at least 40+ cells
    // This ensures we have room to place all 8 numbers along the path
    const path = generateValidPath();
    
    // Step 2: Pick a random starting position from the path (not the last few)
    const startIndex = Math.floor(Math.random() * Math.min(10, path.length - 30));
    const startPos = path[startIndex];
    snakeHead.value = { ...startPos };
    
    // Step 3: Place numbers 1-8 along the remaining path
    placeNumbersAlongPath(path, startIndex);
    
    // Update grid visualization
    updateGridVisualization();
};

// Generate a valid random path using random walk
const generateValidPath = (): Position[] => {
    const path: Position[] = [];
    const visited = new Set<string>();
    
    // Start from a random position
    let current: Position = {
        row: Math.floor(Math.random() * GRID_SIZE),
        col: Math.floor(Math.random() * GRID_SIZE)
    };
    
    path.push({ ...current });
    visited.add(`${current.row},${current.col}`);
    
    // Try to build a long path (at least 40 cells)
    let attempts = 0;
    const maxAttempts = 1000;
    
    while (path.length < 50 && attempts < maxAttempts) {
        attempts++;
        const validMoves = getValidMovesForPathGeneration(current, visited);
        
        if (validMoves.length === 0) {
            // Dead end - try to backtrack and continue from earlier position
            if (path.length > 10) {
                const backtrackSteps = Math.floor(Math.random() * 5) + 1;
                for (let i = 0; i < backtrackSteps && path.length > 1; i++) {
                    const removed = path.pop()!;
                    visited.delete(`${removed.row},${removed.col}`);
                }
                current = path[path.length - 1];
            } else {
                break;
            }
        } else {
            // Pick a random valid move
            const nextMove = validMoves[Math.floor(Math.random() * validMoves.length)];
            path.push({ ...nextMove });
            visited.add(`${nextMove.row},${nextMove.col}`);
            current = nextMove;
        }
    }
    
    return path;
};

// Get valid moves for path generation (not visited yet)
const getValidMovesForPathGeneration = (pos: Position, visited: Set<string>): Position[] => {
    const moves: Position[] = [];
    const directions = [
        { row: -1, col: 0 }, // up
        { row: 1, col: 0 },  // down
        { row: 0, col: -1 }, // left
        { row: 0, col: 1 }   // right
    ];
    
    for (const dir of directions) {
        const newRow = pos.row + dir.row;
        const newCol = pos.col + dir.col;
        
        if (newRow >= 0 && newRow < GRID_SIZE && 
            newCol >= 0 && newCol < GRID_SIZE &&
            !visited.has(`${newRow},${newCol}`)) {
            moves.push({ row: newRow, col: newCol });
        }
    }
    
    return moves;
};

// Place numbers 1-8 strategically along the path after start position
const placeNumbersAlongPath = (path: Position[], startIndex: number) => {
    // We need to place 8 numbers along the remaining path
    // Distribute them somewhat evenly but with some randomness
    const remainingPath = path.slice(startIndex + 1);
    
    if (remainingPath.length < 8) {
        console.error('Path too short!', remainingPath.length);
        // Fallback: place them as best as we can
        for (let i = 0; i < Math.min(8, remainingPath.length); i++) {
            const pos = remainingPath[i];
            grid.value[pos.row][pos.col].number = i + 1;
        }
        return;
    }
    
    // Calculate spacing
    const spacing = Math.floor(remainingPath.length / 8);
    
    for (let i = 0; i < 8; i++) {
        let index = i * spacing + Math.floor(Math.random() * Math.max(1, spacing / 2));
        index = Math.min(index, remainingPath.length - 1);
        const pos = remainingPath[index];
        grid.value[pos.row][pos.col].number = i + 1;
    }
};

// Update grid visualization based on current game state
const updateGridVisualization = () => {
    // Clear all flags
    for (let row = 0; row < GRID_SIZE; row++) {
        for (let col = 0; col < GRID_SIZE; col++) {
            grid.value[row][col].isSnakeHead = false;
            grid.value[row][col].isSnakeBody = false;
        }
    }
    
    // Mark snake head
    grid.value[snakeHead.value.row][snakeHead.value.col].isSnakeHead = true;
    
    // Mark snake body
    for (const pos of snakeBody.value) {
        grid.value[pos.row][pos.col].isSnakeBody = true;
    }
};

// Get valid moves from current position
const getValidMoves = (): Position[] => {
    const moves: Position[] = [];
    const directions = [
        { row: -1, col: 0 }, // up
        { row: 1, col: 0 },  // down
        { row: 0, col: -1 }, // left
        { row: 0, col: 1 }   // right
    ];
    
    for (const dir of directions) {
        const newRow = snakeHead.value.row + dir.row;
        const newCol = snakeHead.value.col + dir.col;
        
        // Check bounds
        if (newRow < 0 || newRow >= GRID_SIZE || newCol < 0 || newCol >= GRID_SIZE) {
            continue;
        }
        
        // Check if already visited (snake body)
        const isVisited = snakeBody.value.some(pos => pos.row === newRow && pos.col === newCol);
        if (isVisited) {
            continue;
        }
        
        moves.push({ row: newRow, col: newCol });
    }
    
    return moves;
};

// Check if game is lost (no valid moves)
const checkGameOver = () => {
    const validMoves = getValidMoves();
    if (validMoves.length === 0 && gameState.value === 'playing') {
        gameState.value = 'lost';
    }
};

// Move snake in a direction
const moveSnake = (direction: 'up' | 'down' | 'left' | 'right') => {
    if (gameState.value !== 'playing') {
        return;
    }
    
    const directionMap = {
        up: { row: -1, col: 0 },
        down: { row: 1, col: 0 },
        left: { row: 0, col: -1 },
        right: { row: 0, col: 1 }
    };
    
    const dir = directionMap[direction];
    const newRow = snakeHead.value.row + dir.row;
    const newCol = snakeHead.value.col + dir.col;
    
    // Check bounds
    if (newRow < 0 || newRow >= GRID_SIZE || newCol < 0 || newCol >= GRID_SIZE) {
        return;
    }
    
    // Check if moving into snake body (game over)
    const isVisited = snakeBody.value.some(pos => pos.row === newRow && pos.col === newCol);
    if (isVisited) {
        return; // Invalid move
    }
    
    // Add current head position to body
    snakeBody.value.push({ ...snakeHead.value });
    
    // Move head to new position
    snakeHead.value = { row: newRow, col: newCol };
    moves.value++;
    
    // Check if we collected a number
    const cell = grid.value[newRow][newCol];
    if (cell.number === currentTarget.value) {
        // Collected correct number!
        cell.number = undefined; // Remove number from grid
        currentTarget.value++;
        
        // Check if won (collected all 8 numbers)
        if (currentTarget.value > 8) {
            gameState.value = 'won';
        }
    }
    
    // Update visualization
    updateGridVisualization();
    
    // Check for game over
    checkGameOver();
};

// Keyboard controls
const handleKeyDown = (event: KeyboardEvent) => {
    // Only handle arrow keys
    if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(event.key)) {
        return;
    }
    
    // Prevent default scrolling behavior
    event.preventDefault();
    
    if (gameState.value !== 'playing') {
        return;
    }
    
    switch (event.key) {
        case 'ArrowUp':
            moveSnake('up');
            break;
        case 'ArrowDown':
            moveSnake('down');
            break;
        case 'ArrowLeft':
            moveSnake('left');
            break;
        case 'ArrowRight':
            moveSnake('right');
            break;
    }
};

// Get cell style classes
const getCellClass = (cell: Cell) => {
    if (cell.isSnakeHead) {
        return 'bg-gradient-to-br from-purple-600 to-purple-800 border-4 border-yellow-400 shadow-lg shadow-purple-500/50';
    }
    if (cell.isSnakeBody) {
        return 'bg-gradient-to-br from-purple-700 to-purple-900 border-2 border-purple-500';
    }
    if (cell.number !== undefined) {
        // Different colors for numbers
        const colors = [
            'from-red-500 to-red-700',      // 1
            'from-orange-500 to-orange-700', // 2
            'from-yellow-500 to-yellow-700', // 3
            'from-green-500 to-green-700',   // 4
            'from-teal-500 to-teal-700',     // 5
            'from-blue-500 to-blue-700',     // 6
            'from-indigo-500 to-indigo-700', // 7
            'from-pink-500 to-pink-700'      // 8
        ];
        return `bg-gradient-to-br ${colors[cell.number - 1]} border-2 border-yellow-400 shadow-md`;
    }
    return 'bg-gradient-to-br from-teal-800 to-teal-900 border border-teal-700/50';
};

// Get cell content (emoji or number)
const getCellContent = (cell: Cell) => {
    if (cell.isSnakeHead) {
        return '🐍'; // Snake head
    }
    if (cell.isSnakeBody) {
        return ''; // Snake body
    }
    if (cell.number !== undefined) {
        return cell.number.toString();
    }
    return '';
};

// Reset game
const resetGame = () => {
    generateSolvableBoard();
};

onMounted(() => {
    generateSolvableBoard();
    window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
    <Head title="Snakedo - Sandbox" />
    
    <div class="min-h-screen bg-gradient-to-br from-teal-900 via-purple-900 to-indigo-900 relative overflow-hidden">
        <!-- Caribbean Voodoo decorations -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-10 left-10 text-yellow-300 opacity-20 text-9xl">💀</div>
            <div class="absolute top-40 right-20 text-purple-400 opacity-15 text-8xl">🗿</div>
            <div class="absolute bottom-20 left-1/4 text-red-400 opacity-20 text-7xl">💀</div>
            <div class="absolute bottom-40 right-1/3 text-teal-300 opacity-15 text-9xl">🗿</div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-yellow-400 opacity-10 text-[20rem]">💀</div>
        </div>

        <div class="container mx-auto px-4 py-8 relative z-10">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-6xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 via-purple-300 to-teal-300 mb-2 font-serif drop-shadow-lg">
                    🐍 SNAKEDO 🗿
                </h1>
                <p class="text-2xl text-yellow-200 font-serif italic">Caribbean Voodoo Snake Challenge</p>
            </div>

            <!-- Game Container -->
            <div class="max-w-4xl mx-auto">
                <!-- Stats and Controls -->
                <div class="bg-gradient-to-r from-purple-900/80 to-teal-900/80 backdrop-blur-sm rounded-lg shadow-2xl p-6 mb-6 border-4 border-yellow-400">
                    <div class="flex justify-between items-center gap-4 flex-wrap">
                        <div class="text-center">
                            <p class="text-sm text-yellow-200 font-serif mb-1">Moves</p>
                            <p class="text-4xl font-bold text-yellow-300">{{ moves }}</p>
                        </div>
                        
                        <div class="text-center">
                            <p class="text-sm text-purple-200 font-serif mb-1">Target Number</p>
                            <p class="text-5xl font-bold text-purple-300">{{ currentTarget > 8 ? '✓' : currentTarget }}</p>
                        </div>

                        <div class="text-center">
                            <p class="text-sm text-teal-200 font-serif mb-1">Collected</p>
                            <p class="text-4xl font-bold text-teal-300">{{ currentTarget - 1 }} / 8</p>
                        </div>

                        <button
                            @click="resetGame"
                            class="px-8 py-4 rounded-lg font-bold text-xl shadow-lg transition-all duration-300 font-serif border-4 bg-gradient-to-r from-yellow-500 to-orange-500 text-purple-900 border-yellow-300 hover:shadow-xl hover:scale-105 active:scale-95"
                        >
                            🔄 New Game
                        </button>
                    </div>
                    
                    <!-- Win Message -->
                    <div v-if="gameState === 'won'" class="mt-4 p-4 bg-gradient-to-r from-yellow-400 to-yellow-600 border-4 border-yellow-200 rounded-lg animate-pulse">
                        <p class="text-3xl font-bold text-center text-purple-900 font-serif">
                            🎉 VOODOO MASTER! 🎉
                        </p>
                        <p class="text-center text-purple-800 mt-2 font-serif text-xl">
                            You conquered the snake in {{ moves }} moves!
                        </p>
                    </div>

                    <!-- Lose Message -->
                    <div v-if="gameState === 'lost'" class="mt-4 p-4 bg-gradient-to-r from-red-600 to-red-800 border-4 border-red-400 rounded-lg">
                        <p class="text-3xl font-bold text-center text-white font-serif">
                            💀 TRAPPED! 💀
                        </p>
                        <p class="text-center text-red-100 mt-2 font-serif text-xl">
                            The snake ate itself! Try again!
                        </p>
                    </div>
                </div>

                <!-- Game Grid -->
                <div class="bg-gradient-to-br from-purple-950 to-teal-950 p-6 rounded-2xl shadow-2xl border-4 border-yellow-400">
                    <div class="grid grid-cols-9 gap-1 bg-black/30 p-4 rounded-xl">
                        <template v-for="(row, rowIndex) in grid" :key="`row-${rowIndex}`">
                            <template v-for="(cell, colIndex) in row" :key="`cell-${rowIndex}-${colIndex}`">
                                <div
                                    :class="[
                                        'aspect-square rounded-md flex items-center justify-center text-2xl font-bold transition-all duration-200 font-serif',
                                        getCellClass(cell)
                                    ]"
                                >
                                    <span v-if="cell.isSnakeHead" class="text-4xl">{{ getCellContent(cell) }}</span>
                                    <span v-else-if="cell.number !== undefined" class="text-white text-3xl drop-shadow-lg">{{ getCellContent(cell) }}</span>
                                    <span v-else>{{ getCellContent(cell) }}</span>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mt-6 bg-gradient-to-r from-purple-900/80 to-teal-900/80 backdrop-blur-sm rounded-lg shadow-2xl p-6 border-2 border-yellow-400">
                    <h3 class="text-2xl font-bold text-yellow-300 mb-4 font-serif flex items-center gap-2">
                        <span>🗿</span> How to Play <span>💀</span>
                    </h3>
                    <ul class="text-yellow-100 space-y-2 font-serif text-lg">
                        <li class="flex items-start">
                            <span class="mr-3 text-2xl">⌨️</span>
                            <span>Use <kbd class="px-2 py-1 bg-purple-800 rounded border border-purple-600 text-yellow-300">Arrow Keys</kbd> to move the snake</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-3 text-2xl">🎯</span>
                            <span>Eat numbers <strong>in order</strong> from 1 to 8</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-3 text-2xl">🐍</span>
                            <span>Your snake grows longer with each move - don't cross your tail!</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-3 text-2xl">🧠</span>
                            <span><strong>Plan ahead!</strong> Study the board before you start moving</span>
                        </li>
                        <li class="flex items-start">
                            <span class="mr-3 text-2xl">🏆</span>
                            <span>Collect all 8 numbers without getting trapped to win!</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Voodoo Particles Animation (on win) -->
        <Transition
            enter-active-class="transition-opacity duration-500"
            leave-active-class="transition-opacity duration-1000"
            enter-from-class="opacity-0"
            leave-to-class="opacity-0"
        >
            <div v-if="gameState === 'won'" class="fixed inset-0 pointer-events-none z-50">
                <!-- Floating skulls and tikis -->
                <div
                    v-for="n in 15"
                    :key="n"
                    :class="[
                        'absolute animate-ping text-6xl',
                        n % 3 === 0 ? 'text-yellow-400' : n % 3 === 1 ? 'text-purple-400' : 'text-teal-400'
                    ]"
                    :style="{
                        left: `${(n * 67 + 10) % 90}%`,
                        top: `${(n * 43 + 15) % 70}%`,
                        animationDelay: `${(n * 0.25) % 2}s`,
                        animationDuration: '2s'
                    }"
                >
                    {{ n % 2 === 0 ? '💀' : '🗿' }}
                </div>
                
                <!-- Sparkles -->
                <div
                    v-for="n in 20"
                    :key="`sparkle-${n}`"
                    class="absolute text-yellow-300 text-3xl animate-bounce"
                    :style="{
                        left: `${(n * 5) % 100}%`,
                        top: `${(n * 3) % 80}%`,
                        animationDelay: `${(n * 0.15) % 3}s`,
                        animationDuration: '1.5s'
                    }"
                >
                    ✨
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
kbd {
    font-family: monospace;
}
</style>
