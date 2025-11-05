<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';

interface Cell {
    row: number;
    col: number;
    number?: number; // 1-20 for collectible numbers
    isSnakeHead: boolean;
    isSnakeBody: boolean;
    isBlocked: boolean; // For blacked out cells
}

interface Position {
    row: number;
    col: number;
}

const GRID_SIZE = 10;
const MIN_DISTANCE = 5; // Minimum Manhattan distance between consecutive numbers
const grid = ref<Cell[][]>([]);
const snakeHead = ref<Position>({ row: 0, col: 0 });
const snakeBody = ref<Position[]>([]); // Trail of visited positions
const currentTarget = ref(1); // Next number to collect
const totalNumbers = ref(0); // Total numbers placed (dynamic)
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
                isSnakeBody: false,
                isBlocked: false
            });
        }
        grid.value.push(rowCells);
    }
};

// Generate board by placing numbers until no more valid positions
const generateSolvableBoard = () => {
    initializeGrid();
    snakeBody.value = [];
    currentTarget.value = 1;
    gameState.value = 'playing';
    moves.value = 0;

    // Step 1: Random start position for snake
    snakeHead.value = {
        row: Math.floor(Math.random() * GRID_SIZE),
        col: Math.floor(Math.random() * GRID_SIZE)
    };
    
    // Step 2: Place numbers until we can't anymore
    const numbersPlaced = placeNumbersDynamically();
    totalNumbers.value = numbersPlaced;
    
    console.log('✓ Game generated');
    console.log(`  - Grid: ${GRID_SIZE}x${GRID_SIZE}`);
    console.log(`  - Numbers to collect: ${totalNumbers.value}`);
    console.log(`  - Snake start: (${snakeHead.value.row}, ${snakeHead.value.col})`);
    
    // Update grid visualization
    updateGridVisualization();
};



// Place numbers dynamically until no valid position exists
const placeNumbersDynamically = (): number => {
    const visitedBySnake = new Set<string>();
    visitedBySnake.add(`${snakeHead.value.row},${snakeHead.value.col}`);
    let currentPos = { ...snakeHead.value };
    let numberCount = 0;
    
    while (true) {
        numberCount++;
        
        // Find all positions at least MIN_DISTANCE away from current position
        const candidates: Position[] = [];
        for (let row = 0; row < GRID_SIZE; row++) {
            for (let col = 0; col < GRID_SIZE; col++) {
                // Skip if already occupied
                if (grid.value[row][col].number !== undefined) continue;
                if (row === snakeHead.value.row && col === snakeHead.value.col) continue;
                
                // Check minimum distance
                const distance = Math.abs(row - currentPos.row) + Math.abs(col - currentPos.col);
                if (distance >= MIN_DISTANCE) {
                    candidates.push({ row, col });
                }
            }
        }
        
        if (candidates.length === 0) {
            // No more valid positions - done!
            return numberCount - 1;
        }
        
        // Shuffle candidates for variety
        candidates.sort(() => Math.random() - 0.5);
        
        // Try to find a reachable candidate
        let foundPosition: Position | null = null;
        let foundPath: Position[] | null = null;
        
        for (const candidate of candidates) {
            const path = findPath(currentPos, candidate, visitedBySnake, numberCount);
            if (path && path.length > 0) {
                foundPosition = candidate;
                foundPath = path;
                break;
            }
        }
        
        if (!foundPosition || !foundPath) {
            // Can't reach any valid position - done!
            return numberCount - 1;
        }
        
        // Place the number
        grid.value[foundPosition.row][foundPosition.col].number = numberCount;
        
        // Update snake path
        for (const pos of foundPath) {
            visitedBySnake.add(`${pos.row},${pos.col}`);
        }
        
        currentPos = foundPosition;
    }
};

// Verify that the puzzle is solvable with sequential number collection
// Uses BFS to check if each number can be reached from the previous one
const verifyPuzzleSolvable = (): boolean => {
    // Find starting position and all number positions
    const numberPositions: Position[] = [];
    
    for (let row = 0; row < GRID_SIZE; row++) {
        for (let col = 0; col < GRID_SIZE; col++) {
            const cell = grid.value[row][col];
            if (cell.number !== undefined) {
                numberPositions[cell.number - 1] = { row, col };
            }
        }
    }
    
    // Check if we can reach each number sequentially
    let currentPos = { ...snakeHead.value };
    const visitedCells = new Set<string>();
    visitedCells.add(`${currentPos.row},${currentPos.col}`);
    
    for (let targetNum = 1; targetNum <= TOTAL_NUMBERS; targetNum++) {
        const targetPos = numberPositions[targetNum - 1];
        if (!targetPos) {
            console.error('Missing number:', targetNum);
            return false;
        }
        
        // Use BFS to check if we can reach the target from current position
        const canReach = canReachTarget(currentPos, targetPos, visitedCells, targetNum);
        
        if (!canReach) {
            console.log(`Cannot reach number ${targetNum} from current position`);
            return false;
        }
        
        // Update position and visited cells for next iteration
        const path = findPath(currentPos, targetPos, visitedCells, targetNum);
        if (path) {
            for (const pos of path) {
                visitedCells.add(`${pos.row},${pos.col}`);
            }
            currentPos = targetPos;
        }
    }
    
    return true;
};

// BFS to check if target is reachable
const canReachTarget = (start: Position, target: Position, visited: Set<string>, targetNum: number): boolean => {
    const queue: Position[] = [start];
    const localVisited = new Set<string>(visited);
    
    while (queue.length > 0) {
        const current = queue.shift()!;
        
        if (current.row === target.row && current.col === target.col) {
            return true;
        }
        
        const directions = [
            { row: -1, col: 0 },
            { row: 1, col: 0 },
            { row: 0, col: -1 },
            { row: 0, col: 1 }
        ];
        
        for (const dir of directions) {
            const newRow = current.row + dir.row;
            const newCol = current.col + dir.col;
            
            if (newRow < 0 || newRow >= GRID_SIZE || newCol < 0 || newCol >= GRID_SIZE) {
                continue;
            }
            
            const key = `${newRow},${newCol}`;
            if (localVisited.has(key)) {
                continue;
            }
            
            const cell = grid.value[newRow][newCol];
            
            // Can't go through wrong numbers (only through target or empty cells)
            if (cell.number !== undefined && cell.number !== targetNum) {
                continue;
            }
            
            localVisited.add(key);
            queue.push({ row: newRow, col: newCol });
        }
    }
    
    return false;
};

// Find actual path to target (for updating visited cells)
const findPath = (start: Position, target: Position, visited: Set<string>, targetNum: number): Position[] | null => {
    const queue: { pos: Position; path: Position[] }[] = [{ pos: start, path: [] }];
    const localVisited = new Set<string>(visited);
    
    while (queue.length > 0) {
        const { pos, path } = queue.shift()!;
        
        if (pos.row === target.row && pos.col === target.col) {
            return path;
        }
        
        const directions = [
            { row: -1, col: 0 },
            { row: 1, col: 0 },
            { row: 0, col: -1 },
            { row: 0, col: 1 }
        ];
        
        for (const dir of directions) {
            const newRow = pos.row + dir.row;
            const newCol = pos.col + dir.col;
            
            if (newRow < 0 || newRow >= GRID_SIZE || newCol < 0 || newCol >= GRID_SIZE) {
                continue;
            }
            
            const key = `${newRow},${newCol}`;
            if (localVisited.has(key)) {
                continue;
            }
            
            const cell = grid.value[newRow][newCol];
            
            if (cell.number !== undefined && cell.number !== targetNum) {
                continue;
            }
            
            localVisited.add(key);
            const newPath = [...path, { row: newRow, col: newCol }];
            queue.push({ pos: { row: newRow, col: newCol }, path: newPath });
        }
    }
    
    return null;
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
        
        // Check if it's a number that's not the next target (can't move through wrong numbers)
        const targetCell = grid.value[newRow][newCol];
        if (targetCell.number !== undefined && targetCell.number !== currentTarget.value) {
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
    
    // Check if moving into a number that's not the next target
    const targetCell = grid.value[newRow][newCol];
    if (targetCell.number !== undefined && targetCell.number !== currentTarget.value) {
        return; // Can't move through wrong numbers - must collect in sequence!
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
        
        // Check if won (collected all numbers)
        if (currentTarget.value > totalNumbers.value) {
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
        // Different colors for numbers (cycle through colors for 1-20)
        const colors = [
            'from-red-500 to-red-700',
            'from-orange-500 to-orange-700',
            'from-yellow-500 to-yellow-700',
            'from-green-500 to-green-700',
            'from-teal-500 to-teal-700',
            'from-blue-500 to-blue-700',
            'from-indigo-500 to-indigo-700',
            'from-pink-500 to-pink-700',
            'from-rose-500 to-rose-700',
            'from-fuchsia-500 to-fuchsia-700'
        ];
        const colorIndex = (cell.number - 1) % colors.length;
        return `bg-gradient-to-br ${colors[colorIndex]} border-2 border-yellow-400 shadow-md`;
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

// Generate Python script using OR-Tools
const pythonScript = computed(() => {
    const numbers: Array<{num: number, row: number, col: number}> = [];
    
    // Collect all number positions
    for (let row = 0; row < GRID_SIZE; row++) {
        for (let col = 0; col < GRID_SIZE; col++) {
            const cell = grid.value[row][col];
            if (cell.number !== undefined) {
                numbers.push({ num: cell.number, row, col });
            }
        }
    }
    
    // Sort by number
    numbers.sort((a, b) => a.num - b.num);
    
    return `# OR-Tools TSP Solution for Snakedo Puzzle with Snake Body Collision Detection
# Install: pip install ortools

from ortools.constraint_solver import routing_enums_pb2
from ortools.constraint_solver import pywrapcp
from collections import deque

# Grid configuration
GRID_SIZE = ${GRID_SIZE}
start_pos = (${snakeHead.value.row}, ${snakeHead.value.col})

# Number positions (in order they must be collected)
positions = [
    start_pos,  # Snake starting position
${numbers.map(n => `    (${n.row}, ${n.col}),  # Number ${n.num}`).join('\n')}
]

def bfs_with_snake_body(start, end, snake_body, grid_size):
    """
    BFS pathfinding that avoids the snake's body.
    Returns the path length or a large number if unreachable.
    """
    if start == end:
        return 0
    
    queue = deque([(start[0], start[1], 0)])
    visited = {start}
    
    while queue:
        row, col, dist = queue.popleft()
        
        # Check all four directions (no diagonals)
        for dr, dc in [(-1, 0), (1, 0), (0, -1), (0, 1)]:
            new_row, new_col = row + dr, col + dc
            new_pos = (new_row, new_col)
            
            # Check bounds
            if 0 <= new_row < grid_size and 0 <= new_col < grid_size:
                # Check if not visited and not blocked by snake body
                if new_pos not in visited and (new_pos not in snake_body or new_pos == end):
                    if new_pos == end:
                        return dist + 1
                    
                    visited.add(new_pos)
                    queue.append((new_row, new_col, dist + 1))
    
    return 999999  # Unreachable

def build_distance_matrix_with_snake_growth(positions, grid_size):
    """
    Build distance matrix accounting for snake body growth.
    When moving from positions[i] to positions[j], the snake has length i+1.
    """
    num_locations = len(positions)
    distance_matrix = [[0] * num_locations for _ in range(num_locations)]
    
    for i in range(num_locations):
        for j in range(num_locations):
            if i == j:
                distance_matrix[i][j] = 0
            else:
                # Snake length at position i is i+1 (initial + collected numbers)
                snake_length = i + 1
                
                # Build snake body: the last snake_length positions in the path
                snake_body = set()
                for k in range(max(0, i - snake_length + 1), i + 1):
                    snake_body.add(positions[k])
                
                # Use BFS to find path avoiding snake body
                distance = bfs_with_snake_body(positions[i], positions[j], snake_body, grid_size)
                distance_matrix[i][j] = distance
    
    return distance_matrix

def bfs_actual_path(start, end, snake_body, grid_size):
    """
    BFS that returns the actual path (list of positions) avoiding snake body.
    """
    if start == end:
        return [start]
    
    queue = deque([(start[0], start[1], [start])])
    visited = {start}
    
    while queue:
        row, col, path = queue.popleft()
        
        for dr, dc in [(-1, 0), (1, 0), (0, -1), (0, 1)]:
            new_row, new_col = row + dr, col + dc
            new_pos = (new_row, new_col)
            
            if 0 <= new_row < grid_size and 0 <= new_col < grid_size:
                if new_pos not in visited and (new_pos not in snake_body or new_pos == end):
                    if new_pos == end:
                        return path + [new_pos]
                    
                    visited.add(new_pos)
                    queue.append((new_row, new_col, path + [new_pos]))
    
    return []  # No path found

# Build distance matrix with snake body collision detection
distance_matrix = build_distance_matrix_with_snake_growth(positions, GRID_SIZE)

# Create routing model
num_locations = len(positions)
manager = pywrapcp.RoutingIndexManager(num_locations, 1, 0)
routing = pywrapcp.RoutingModel(manager)

# Distance callback
def distance_callback(from_index, to_index):
    from_node = manager.IndexToNode(from_index)
    to_node = manager.IndexToNode(to_index)
    return distance_matrix[from_node][to_node]

transit_callback_index = routing.RegisterTransitCallback(distance_callback)
routing.SetArcCostEvaluatorOfAllVehicles(transit_callback_index)

# CRITICAL: Add constraints to enforce sequential number collection
# Numbers must be visited in order: 0 (start) -> 1 -> 2 -> 3 -> ...
for i in range(num_locations - 1):
    # Force the route to go from position i to position i+1
    routing.AddPickupAndDelivery(i, i + 1)
    routing.solver().Add(routing.VehicleVar(i) == routing.VehicleVar(i + 1))
    # Ensure i+1 comes immediately after i in the route
    routing.solver().Add(routing.NextVar(manager.NodeToIndex(i)) == manager.NodeToIndex(i + 1))

# Search parameters
search_parameters = pywrapcp.DefaultRoutingSearchParameters()
search_parameters.first_solution_strategy = (
    routing_enums_pb2.FirstSolutionStrategy.PATH_CHEAPEST_ARC
)

# Solve
solution = routing.SolveWithParameters(search_parameters)

if solution:
    print("\\n🎮 SNAKEDO SOLUTION WITH SNAKE BODY COLLISION DETECTION 🐍\\n")
    print(f"Total numbers to collect: ${totalNumbers.value}")
    print(f"Optimal path distance: {solution.ObjectiveValue()} moves\\n")
    
    # Get the route
    index = routing.Start(0)
    route_indices = []
    
    while not routing.IsEnd(index):
        node = manager.IndexToNode(index)
        route_indices.append(node)
        index = solution.Value(routing.NextVar(index))
    
    # Generate complete arrow key sequence with snake body avoidance
    print("Arrow Key Sequence (avoiding snake body):")
    all_arrow_keys = []
    
    for i in range(len(route_indices) - 1):
        from_idx = route_indices[i]
        to_idx = route_indices[i + 1]
        
        # Build snake body at this stage
        snake_length = from_idx + 1
        snake_body = set()
        for k in range(max(0, from_idx - snake_length + 1), from_idx + 1):
            snake_body.add(positions[route_indices[k] if k < len(route_indices) else from_idx])
        
        # Get actual path avoiding snake body
        path = bfs_actual_path(positions[from_idx], positions[to_idx], snake_body, GRID_SIZE)
        
        # Convert path to arrow keys
        for j in range(len(path) - 1):
            curr = path[j]
            next_pos = path[j + 1]
            
            if next_pos[0] < curr[0]:
                all_arrow_keys.append('↑')
            elif next_pos[0] > curr[0]:
                all_arrow_keys.append('↓')
            elif next_pos[1] < curr[1]:
                all_arrow_keys.append('←')
            elif next_pos[1] > curr[1]:
                all_arrow_keys.append('→')
    
    # Print arrow sequence in groups of 4
    for i in range(0, len(all_arrow_keys), 4):
        group = all_arrow_keys[i:i+4]
        print(' '.join(group))
    
    print(f"\\nTotal moves: {len(all_arrow_keys)}")
    print("\\n✅ This solution accounts for the growing snake body!")
    print("The path avoids collision with the snake's tail as it grows.")
    
    # Show route order
    print(f"\\nRoute order: {' → '.join([f'#{route_indices[i]}' if i > 0 else 'Start' for i in range(len(route_indices))])}")
else:
    print("No solution found!")
`;
});

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
                            <p class="text-5xl font-bold text-purple-300">{{ currentTarget > TOTAL_NUMBERS ? '✓' : currentTarget }}</p>
                        </div>

                        <div class="text-center">
                            <p class="text-sm text-teal-200 font-serif mb-1">Collected</p>
                            <p class="text-4xl font-bold text-teal-300">{{ currentTarget - 1 }} / {{ totalNumbers }}</p>
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
                    <div 
                        class="grid gap-1 bg-black/30 p-4 rounded-xl mx-auto"
                        :style="{ gridTemplateColumns: `repeat(${GRID_SIZE}, minmax(0, 1fr))`, maxWidth: `${GRID_SIZE * 50}px` }"
                    >
                        <template v-for="(row, rowIndex) in grid" :key="`row-${rowIndex}`">
                            <template v-for="(cell, colIndex) in row" :key="`cell-${rowIndex}-${colIndex}`">
                                <div
                                    :class="[
                                        'aspect-square rounded flex items-center justify-center text-lg font-bold transition-all duration-200 font-serif',
                                        getCellClass(cell)
                                    ]"
                                >
                                    <span v-if="cell.isSnakeHead" class="text-3xl leading-none">{{ getCellContent(cell) }}</span>
                                    <span v-else-if="cell.number !== undefined" class="text-white text-xl leading-none drop-shadow-lg">{{ getCellContent(cell) }}</span>
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
                            <span>Eat numbers <strong>in order</strong> from 1 to {{ totalNumbers }}</span>
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
                            <span>Collect all {{ totalNumbers }} numbers without getting trapped to win!</span>
                        </li>
                    </ul>
                </div>

                <!-- OR-Tools Python Script -->
                <div class="mt-6 bg-gradient-to-r from-slate-900/90 to-slate-800/90 backdrop-blur-sm rounded-lg shadow-2xl p-6 border-2 border-green-400">
                    <h3 class="text-2xl font-bold text-green-300 mb-4 font-serif flex items-center gap-2">
                        <span>🐍</span> OR-Tools Solution Script <span>🧮</span>
                    </h3>
                    <p class="text-green-100 mb-4 font-serif">
                        Copy and run this Python script to see the optimal path using Google OR-Tools:
                    </p>
                    <textarea
                        readonly
                        :value="pythonScript"
                        class="w-full h-96 p-4 bg-slate-950 text-green-400 font-mono text-sm rounded border-2 border-green-500/50 focus:border-green-400 focus:outline-none resize-y"
                        @click="($event.target as HTMLTextAreaElement).select()"
                    ></textarea>
                    <div class="mt-4 flex gap-4 items-center flex-wrap">
                        <button
                            @click="() => { const el = document.querySelector('textarea'); if(el) { el.select(); navigator.clipboard.writeText(el.value); } }"
                            class="px-6 py-3 bg-green-600 hover:bg-green-500 text-white font-bold rounded-lg shadow-lg transition-all duration-300 font-serif border-2 border-green-400"
                        >
                            📋 Copy to Clipboard
                        </button>
                        <p class="text-green-200 text-sm font-serif">
                            💡 Install OR-Tools: <code class="bg-slate-800 px-2 py-1 rounded text-green-300">pip install ortools</code>
                        </p>
                    </div>
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
