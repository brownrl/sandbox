<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head } from '@inertiajs/vue3';

type GridShape = 'squares' | 'hexagons' | 'octagons';

interface Cell {
    x: number;
    y: number;
    blocked: boolean;
}

interface PathCell {
    x: number;
    y: number;
    g: number; // distance from start
    h: number; // distance to target
    f: number; // total (g + h)
    isOptimalPath: boolean;
}

const gridSize = 16;
const gridShape = ref<GridShape>('hexagons');
const cells = ref<Cell[]>([]);
const startCell = ref<{ x: number; y: number } | null>(null);
const hoveredCell = ref<{ x: number; y: number } | null>(null);
const pathCells = ref<PathCell[]>([]);

// Initialize grid
const initializeGrid = () => {
    cells.value = [];
    for (let y = 0; y < gridSize; y++) {
        for (let x = 0; x < gridSize; x++) {
            cells.value.push({ x, y, blocked: false });
        }
    }
    startCell.value = null;
    hoveredCell.value = null;
    pathCells.value = [];
};

// Randomize blocked cells (25%)
const randomizeGrid = () => {
    const totalCells = gridSize * gridSize;
    const blockedCount = Math.floor(totalCells * 0.25);
    
    // Reset all cells
    cells.value.forEach(cell => cell.blocked = false);
    
    // Randomly block cells
    const indices = new Set<number>();
    while (indices.size < blockedCount) {
        indices.add(Math.floor(Math.random() * totalCells));
    }
    
    indices.forEach(idx => {
        cells.value[idx].blocked = true;
    });
    
    // Reset start and path
    startCell.value = null;
    hoveredCell.value = null;
    pathCells.value = [];
};

// Get cell at position
const getCellAt = (x: number, y: number): Cell | undefined => {
    return cells.value.find(cell => cell.x === x && cell.y === y);
};

// Check if cell is blocked
const isBlocked = (x: number, y: number): boolean => {
    const cell = getCellAt(x, y);
    return cell?.blocked ?? true;
};

// Get neighbors based on grid shape
const getNeighbors = (x: number, y: number): { x: number; y: number }[] => {
    const neighbors: { x: number; y: number }[] = [];
    
    if (gridShape.value === 'squares') {
        // 4-way movement (up, down, left, right)
        const deltas = [
            { dx: 0, dy: -1 }, // up
            { dx: 0, dy: 1 },  // down
            { dx: -1, dy: 0 }, // left
            { dx: 1, dy: 0 },  // right
        ];
        
        deltas.forEach(({ dx, dy }) => {
            const nx = x + dx;
            const ny = y + dy;
            if (nx >= 0 && nx < gridSize && ny >= 0 && ny < gridSize && !isBlocked(nx, ny)) {
                neighbors.push({ x: nx, y: ny });
            }
        });
    } else if (gridShape.value === 'hexagons') {
        // Flat-top hexagon neighbors (6 directions - NO horizontal, but vertical + diagonals)
        // Can move vertically (up/down through flat sides) and diagonally (through angled sides)
        // This creates zigzag paths for horizontal travel
        const deltas = [
            { dx: 0, dy: -1 },  // N (up through flat top)
            { dx: 0, dy: 1 },   // S (down through flat bottom)
            { dx: -1, dy: -1 }, // NW (up-left angled side)
            { dx: 1, dy: -1 },  // NE (up-right angled side)
            { dx: -1, dy: 1 },  // SW (down-left angled side)
            { dx: 1, dy: 1 },   // SE (down-right angled side)
        ];
        
        deltas.forEach(({ dx, dy }) => {
            const nx = x + dx;
            const ny = y + dy;
            if (nx >= 0 && nx < gridSize && ny >= 0 && ny < gridSize && !isBlocked(nx, ny)) {
                neighbors.push({ x: nx, y: ny });
            }
        });
    } else if (gridShape.value === 'octagons') {
        // 8-way movement (including diagonals)
        const deltas = [
            { dx: 0, dy: -1 },  // up
            { dx: 1, dy: -1 },  // up-right
            { dx: 1, dy: 0 },   // right
            { dx: 1, dy: 1 },   // down-right
            { dx: 0, dy: 1 },   // down
            { dx: -1, dy: 1 },  // down-left
            { dx: -1, dy: 0 },  // left
            { dx: -1, dy: -1 }, // up-left
        ];
        
        deltas.forEach(({ dx, dy }) => {
            const nx = x + dx;
            const ny = y + dy;
            if (nx >= 0 && nx < gridSize && ny >= 0 && ny < gridSize && !isBlocked(nx, ny)) {
                neighbors.push({ x: nx, y: ny });
            }
        });
    }
    
    return neighbors;
};

// Calculate heuristic (different for each grid type)
const calculateHeuristic = (x1: number, y1: number, x2: number, y2: number): number => {
    if (gridShape.value === 'squares') {
        // Manhattan distance for square grid
        return Math.abs(x1 - x2) + Math.abs(y1 - y2);
    } else if (gridShape.value === 'hexagons') {
        // Hexagonal distance calculation for flat-top hex grid
        // Convert offset coordinates to axial/cube coordinates
        const q1 = x1 - (y1 - (y1 & 1)) / 2;
        const r1 = y1;
        const q2 = x2 - (y2 - (y2 & 1)) / 2;
        const r2 = y2;
        
        return (Math.abs(q1 - q2) + Math.abs(q1 + r1 - q2 - r2) + Math.abs(r1 - r2)) / 2;
    } else {
        // Octagonal distance (Chebyshev distance)
        return Math.max(Math.abs(x1 - x2), Math.abs(y1 - y2));
    }
};

// A* pathfinding algorithm
const findPath = (start: { x: number; y: number }, target: { x: number; y: number }) => {
    interface Node {
        x: number;
        y: number;
        g: number;
        h: number;
        f: number;
        parent: Node | null;
    }
    
    const openSet: Node[] = [];
    const closedSet = new Set<string>();
    const explored = new Map<string, Node>();
    
    const nodeKey = (x: number, y: number) => `${x},${y}`;
    
    // Start node
    const startNode: Node = {
        x: start.x,
        y: start.y,
        g: 0,
        h: calculateHeuristic(start.x, start.y, target.x, target.y),
        f: 0,
        parent: null,
    };
    startNode.f = startNode.g + startNode.h;
    
    openSet.push(startNode);
    explored.set(nodeKey(start.x, start.y), startNode);
    
    while (openSet.length > 0) {
        // Get node with lowest f score
        openSet.sort((a, b) => a.f - b.f);
        const current = openSet.shift()!;
        
        const currentKey = nodeKey(current.x, current.y);
        closedSet.add(currentKey);
        
        // Check if we reached the target
        if (current.x === target.x && current.y === target.y) {
            // Reconstruct path
            const optimalPath = new Set<string>();
            let node: Node | null = current;
            while (node) {
                optimalPath.add(nodeKey(node.x, node.y));
                node = node.parent;
            }
            
            // Convert explored nodes to PathCell array
            const result: PathCell[] = [];
            explored.forEach((node, key) => {
                result.push({
                    x: node.x,
                    y: node.y,
                    g: node.g,
                    h: node.h,
                    f: node.f,
                    isOptimalPath: optimalPath.has(key),
                });
            });
            
            return result;
        }
        
        // Check neighbors
        const neighbors = getNeighbors(current.x, current.y);
        
        for (const neighbor of neighbors) {
            const neighborKey = nodeKey(neighbor.x, neighbor.y);
            
            if (closedSet.has(neighborKey)) {
                continue;
            }
            
            const g = current.g + 1;
            const h = calculateHeuristic(neighbor.x, neighbor.y, target.x, target.y);
            const f = g + h;
            
            const existingNode = explored.get(neighborKey);
            
            if (!existingNode || g < existingNode.g) {
                const neighborNode: Node = {
                    x: neighbor.x,
                    y: neighbor.y,
                    g,
                    h,
                    f,
                    parent: current,
                };
                
                explored.set(neighborKey, neighborNode);
                
                if (!openSet.find(n => n.x === neighbor.x && n.y === neighbor.y)) {
                    openSet.push(neighborNode);
                }
            }
        }
    }
    
    // No path found - return all explored nodes
    const result: PathCell[] = [];
    explored.forEach((node) => {
        result.push({
            x: node.x,
            y: node.y,
            g: node.g,
            h: node.h,
            f: node.f,
            isOptimalPath: false,
        });
    });
    return result;
};

// Handle cell click
const handleCellClick = (cell: Cell) => {
    if (cell.blocked) return;
    
    startCell.value = { x: cell.x, y: cell.y };
    hoveredCell.value = null;
    pathCells.value = [];
};

// Handle cell hover
const handleCellHover = (cell: Cell) => {
    if (cell.blocked || !startCell.value) return;
    
    // Don't recalculate if same cell
    if (hoveredCell.value?.x === cell.x && hoveredCell.value?.y === cell.y) return;
    
    hoveredCell.value = { x: cell.x, y: cell.y };
    
    // Run A* algorithm
    pathCells.value = findPath(startCell.value, hoveredCell.value);
};

// Handle cell mouse leave
const handleCellLeave = () => {
    hoveredCell.value = null;
    pathCells.value = [];
};

// Get path cell info
const getPathCell = (x: number, y: number): PathCell | undefined => {
    return pathCells.value.find(pc => pc.x === x && pc.y === y);
};

// Get cell classes
const getCellClasses = (cell: Cell) => {
    if (cell.blocked) {
        return 'bg-gray-950 border-gray-900 cursor-not-allowed opacity-80';
    }
    
    const pathCell = getPathCell(cell.x, cell.y);
    const isStart = startCell.value?.x === cell.x && startCell.value?.y === cell.y;
    const isHovered = hoveredCell.value?.x === cell.x && hoveredCell.value?.y === cell.y;
    
    if (isStart) {
        return 'bg-green-500 border-green-600 cursor-pointer shadow-lg shadow-green-500/50 z-10';
    }
    
    if (isHovered) {
        return 'bg-blue-500 border-blue-600 cursor-pointer shadow-lg shadow-blue-500/50 z-10';
    }
    
    if (pathCell) {
        if (pathCell.isOptimalPath) {
            return 'bg-red-600 border-red-700 cursor-pointer shadow-md shadow-red-600/30 z-[5]';
        } else {
            return 'bg-pink-500 border-pink-600 cursor-pointer shadow-sm opacity-90';
        }
    }
    
    return 'bg-gray-700 border-gray-800 cursor-pointer hover:bg-gray-600 hover:border-gray-700 transition-colors';
};

// Get cell style for positioning
const getCellStyle = (cell: Cell) => {
    const cellSize = 50; // Bigger cells!
    const gap = 2;
    
    if (gridShape.value === 'squares') {
        return {
            width: `${cellSize}px`,
            height: `${cellSize}px`,
            left: `${cell.x * (cellSize + gap)}px`,
            top: `${cell.y * (cellSize + gap)}px`,
        };
    } else if (gridShape.value === 'hexagons') {
        // Flat-top hexagon - simple grid pattern with more spacing
        const hexSize = cellSize;
        const gap = 8; // More space between hexagons
        
        return {
            width: `${hexSize}px`,
            height: `${hexSize}px`,
            left: `${cell.x * (hexSize + gap)}px`,
            top: `${cell.y * (hexSize + gap)}px`,
            clipPath: 'polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%)',
        };
    } else if (gridShape.value === 'octagons') {
        // Octagon + square pattern (proper tessellation)
        const octSize = cellSize;
        const gap = octSize * 0.414; // sqrt(2) - 1 ≈ 0.414 for perfect fit
        const fullSpacing = octSize + gap;
        
        return {
            width: `${octSize}px`,
            height: `${octSize}px`,
            left: `${cell.x * fullSpacing}px`,
            top: `${cell.y * fullSpacing}px`,
            clipPath: 'polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%)',
        };
    }
    
    return {};
};

// Watch for shape changes
watch(gridShape, () => {
    initializeGrid();
});

// Initialize on mount
initializeGrid();
</script>

<template>
    <Head title="A-Star Pathfinding - Sandbox" />
    
    <div class="min-h-screen bg-gradient-to-br from-indigo-950 via-purple-950 to-black relative overflow-hidden">
        <!-- Space background decorations -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-10 left-10 text-yellow-200 opacity-50 text-6xl">⭐</div>
            <div class="absolute top-40 right-20 text-yellow-300 opacity-40 text-4xl">✨</div>
            <div class="absolute bottom-20 left-1/4 text-blue-300 opacity-30 text-5xl">🌟</div>
            <div class="absolute bottom-40 right-1/3 text-purple-300 opacity-40 text-7xl">🌙</div>
            <div class="absolute top-1/3 right-10 text-indigo-300 opacity-30 text-8xl">🤖</div>
            <div class="absolute top-2/3 left-20 text-cyan-300 opacity-25 text-6xl">🚀</div>
        </div>

        <div class="container mx-auto px-4 py-8 relative z-10">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400 mb-3 font-mono">
                    A* PATHFINDING
                </h1>
                <p class="text-xl text-cyan-300 font-mono">🤖 Robot Navigation System 🚀</p>
            </div>

            <!-- Controls -->
            <div class="max-w-7xl mx-auto mb-6">
                <div class="bg-gray-900/80 backdrop-blur-sm rounded-lg shadow-2xl p-6 border-2 border-cyan-500">
                    <div class="flex flex-wrap justify-center items-center gap-4">
                        <!-- Shape Selector -->
                        <div class="flex items-center gap-3">
                            <label class="text-cyan-300 font-mono font-bold">Grid Shape:</label>
                            <select
                                v-model="gridShape"
                                class="bg-gray-800 text-cyan-300 border-2 border-cyan-600 rounded-lg px-4 py-2 font-mono focus:outline-none focus:border-cyan-400 transition-colors"
                            >
                                <option value="squares">⬛ Squares</option>
                                <option value="hexagons">⬡ Hexagons</option>
                                <option value="octagons">⬢ Octagons</option>
                            </select>
                        </div>

                        <!-- Randomize Button -->
                        <button
                            @click="randomizeGrid"
                            class="px-6 py-2 rounded-lg font-bold font-mono shadow-lg transition-all duration-300 border-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white border-purple-400 hover:shadow-xl hover:shadow-purple-500/50 hover:scale-105 active:scale-95"
                        >
                            🎲 Randomize
                        </button>
                    </div>

                    <!-- Instructions -->
                    <div class="mt-4 pt-4 border-t border-cyan-800">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm font-mono">
                            <div class="flex items-center gap-2">
                                <span class="text-green-400">🟢</span>
                                <span class="text-cyan-200">Click to set START</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-blue-400">🔵</span>
                                <span class="text-cyan-200">Hover to find path</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-red-500">🔴</span>
                                <span class="text-cyan-200">Optimal path</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid Container -->
            <div class="max-w-7xl mx-auto">
                <div class="bg-gray-950/90 backdrop-blur-sm rounded-2xl shadow-2xl p-8 border-4 border-cyan-600">
                    <div class="relative bg-gray-900 rounded-xl p-8 border-2 border-gray-700 overflow-auto" style="height: 900px;">
                        <div class="relative" style="min-width: 1000px; min-height: 1000px;">
                            <!-- Grid cells -->
                            <div
                                v-for="cell in cells"
                                :key="`${cell.x}-${cell.y}`"
                                :class="[
                                    'absolute flex items-center justify-center transition-colors duration-100',
                                    gridShape === 'hexagons' || gridShape === 'octagons' ? 'border-[1.5px]' : 'border-2',
                                    getCellClasses(cell)
                                ]"
                                :style="getCellStyle(cell)"
                                @click="handleCellClick(cell)"
                                @mouseenter="handleCellHover(cell)"
                                @mouseleave="handleCellLeave"
                            >
                                <!-- Display g, h, f scores -->
                                <div
                                    v-if="getPathCell(cell.x, cell.y) && !cell.blocked"
                                    class="flex flex-col items-center justify-center text-[9px] font-mono font-bold leading-tight select-none pointer-events-none"
                                    :class="getPathCell(cell.x, cell.y)?.isOptimalPath ? 'text-yellow-200' : 'text-white'"
                                >
                                    <div>g:{{ getPathCell(cell.x, cell.y)?.g }}</div>
                                    <div>h:{{ Math.round(getPathCell(cell.x, cell.y)?.h ?? 0) }}</div>
                                    <div class="text-[10px]">f:{{ Math.round(getPathCell(cell.x, cell.y)?.f ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="max-w-7xl mx-auto mt-6">
                <div class="bg-gray-900/80 backdrop-blur-sm rounded-lg shadow-lg p-6 border-2 border-purple-600">
                    <h3 class="text-lg font-bold text-purple-300 mb-3 font-mono">A* Algorithm Legend:</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm font-mono text-cyan-200">
                        <div><span class="font-bold text-yellow-300">g</span> = Distance from START point</div>
                        <div><span class="font-bold text-yellow-300">h</span> = Heuristic distance to TARGET</div>
                        <div><span class="font-bold text-yellow-300">f</span> = Total cost (g + h)</div>
                        <div><span class="text-red-500">■</span> Dark Red = Optimal path (lowest f-score)</div>
                        <div><span class="text-pink-400">■</span> Pink = Explored but not optimal</div>
                        <div><span class="text-gray-600">■</span> Grey = Unvisited cell</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
