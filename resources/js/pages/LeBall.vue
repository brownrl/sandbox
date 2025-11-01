<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import Matter from 'matter-js';

const canvasRef = ref<HTMLCanvasElement | null>(null);
const gameStarted = ref(false);
const gameWon = ref(false);
const showCelebration = ref(false);

let engine: Matter.Engine;
let render: Matter.Render;
let runner: Matter.Runner;
let ball: Matter.Body;
let startBucket: Matter.Body[] = [];
let targetBucket: Matter.Body[] = [];
let draggableShapes: Matter.Body[] = [];
let bucketBottom: Matter.Body | null = null;

interface DraggableShape {
    body: Matter.Body;
    isPlaced: boolean;
    originalX: number;
    originalY: number;
}

const shapes = ref<DraggableShape[]>([]);
const isDragging = ref(false);
const draggedShape = ref<DraggableShape | null>(null);
const dragOffset = ref({ x: 0, y: 0 });

const initGame = () => {
    if (!canvasRef.value) return;

    // Create engine
    engine = Matter.Engine.create();
    engine.gravity.y = 1;

    // Create renderer
    render = Matter.Render.create({
        canvas: canvasRef.value,
        engine: engine,
        options: {
            width: 800,
            height: 600,
            wireframes: false,
            background: '#ffffff',
        },
    });

    // Create walls (boundaries)
    const walls = [
        Matter.Bodies.rectangle(400, 0, 800, 10, { isStatic: true, render: { fillStyle: '#000000' } }), // top
        Matter.Bodies.rectangle(400, 600, 800, 10, { isStatic: true, render: { fillStyle: '#000000' } }), // bottom
        Matter.Bodies.rectangle(0, 300, 10, 600, { isStatic: true, render: { fillStyle: '#000000' } }), // left
        Matter.Bodies.rectangle(800, 300, 10, 600, { isStatic: true, render: { fillStyle: '#000000' } }), // right
    ];

    // Create start bucket (top-left)
    const bucketLeft = Matter.Bodies.rectangle(70, 120, 10, 80, { 
        isStatic: true, 
        render: { fillStyle: '#000000' } 
    });
    const bucketRight = Matter.Bodies.rectangle(130, 120, 10, 80, { 
        isStatic: true, 
        render: { fillStyle: '#000000' } 
    });
    bucketBottom = Matter.Bodies.rectangle(100, 160, 60, 10, { 
        isStatic: true, 
        render: { fillStyle: '#000000' } 
    });

    startBucket = [bucketLeft, bucketRight, bucketBottom];

    // Create target bucket (bottom-right)
    const targetLeft = Matter.Bodies.rectangle(670, 540, 10, 80, { 
        isStatic: true, 
        render: { fillStyle: '#000000' },
        label: 'targetBucket'
    });
    const targetRight = Matter.Bodies.rectangle(730, 540, 10, 80, { 
        isStatic: true, 
        render: { fillStyle: '#000000' },
        label: 'targetBucket'
    });
    const targetBottom = Matter.Bodies.rectangle(700, 580, 60, 10, { 
        isStatic: true, 
        render: { fillStyle: '#000000' },
        label: 'targetBucket'
    });

    targetBucket = [targetLeft, targetRight, targetBottom];

    // Create ball
    ball = Matter.Bodies.circle(100, 100, 15, {
        restitution: 0.8,
        friction: 0.001,
        render: { fillStyle: '#000000' },
        label: 'ball'
    });

    // Generate random shapes
    generateRandomShapes();

    // Add all bodies to world
    Matter.World.add(engine.world, [
        ...walls,
        ...startBucket,
        ...targetBucket,
        ball,
    ]);

    // Run the engine
    runner = Matter.Runner.create();
    Matter.Runner.run(runner, engine);
    Matter.Render.run(render);

    // Check for victory
    Matter.Events.on(engine, 'afterUpdate', checkVictory);
};

const generateRandomShapes = () => {
    shapes.value = [];
    draggableShapes = [];

    const startY = 150;
    const spacing = 120;

    for (let i = 0; i < 4; i++) {
        const width = Math.random() * 80 + 40; // 40-120px
        const height = Math.random() * 30 + 10; // 10-40px
        const rotation = Math.random() * Math.PI; // Random rotation
        const x = 250 + (i * spacing);
        const y = startY;

        const shape = Matter.Bodies.rectangle(x, y, width, height, {
            isStatic: true,
            angle: rotation,
            restitution: 0.8,
            friction: 0.3,
            render: { fillStyle: '#666666' },
            label: 'draggable'
        });

        draggableShapes.push(shape);
        shapes.value.push({
            body: shape,
            isPlaced: false,
            originalX: x,
            originalY: y,
        });
    }

    Matter.World.add(engine.world, draggableShapes);
};

const releaseBall = () => {
    if (gameStarted.value) return;

    gameStarted.value = true;
    gameWon.value = false;
    showCelebration.value = false;

    // Remove bucket bottom
    if (bucketBottom) {
        Matter.World.remove(engine.world, bucketBottom);
        bucketBottom = null;
    }
};

const resetGame = () => {
    gameStarted.value = false;
    gameWon.value = false;
    showCelebration.value = false;

    // Remove all bodies
    Matter.World.clear(engine.world, false);
    Matter.Engine.clear(engine);

    // Reinitialize
    initGame();
};

const checkVictory = () => {
    if (gameWon.value || !gameStarted.value) return;

    const ballPos = ball.position;
    const ballVelocity = ball.velocity;

    // Check if ball is in target bucket area
    const inBucketX = ballPos.x > 670 && ballPos.x < 730;
    const inBucketY = ballPos.y > 520 && ballPos.y < 580;
    const isSettled = Math.abs(ballVelocity.x) < 0.5 && Math.abs(ballVelocity.y) < 0.5;

    if (inBucketX && inBucketY && isSettled) {
        gameWon.value = true;
        showCelebration.value = true;
        setTimeout(() => {
            showCelebration.value = false;
        }, 3000);
    }
};

const handleMouseDown = (event: MouseEvent) => {
    if (gameStarted.value) return;

    const rect = canvasRef.value?.getBoundingClientRect();
    if (!rect) return;

    const mouseX = event.clientX - rect.left;
    const mouseY = event.clientY - rect.top;

    // Check if clicking on a draggable shape
    for (const shape of shapes.value) {
        const pos = shape.body.position;
        const bounds = shape.body.bounds;
        const width = bounds.max.x - bounds.min.x;
        const height = bounds.max.y - bounds.min.y;

        if (
            mouseX > pos.x - width / 2 &&
            mouseX < pos.x + width / 2 &&
            mouseY > pos.y - height / 2 &&
            mouseY < pos.y + height / 2
        ) {
            isDragging.value = true;
            draggedShape.value = shape;
            dragOffset.value = {
                x: mouseX - pos.x,
                y: mouseY - pos.y,
            };
            break;
        }
    }
};

const handleMouseMove = (event: MouseEvent) => {
    if (!isDragging.value || !draggedShape.value) return;

    const rect = canvasRef.value?.getBoundingClientRect();
    if (!rect) return;

    const mouseX = event.clientX - rect.left;
    const mouseY = event.clientY - rect.top;

    const newX = mouseX - dragOffset.value.x;
    const newY = mouseY - dragOffset.value.y;

    // Only allow placement in game area (not in starting shapes area)
    if (newY > 200) {
        Matter.Body.setPosition(draggedShape.value.body, { x: newX, y: newY });
        draggedShape.value.isPlaced = true;
    }
};

const handleMouseUp = () => {
    isDragging.value = false;
    draggedShape.value = null;
};

onMounted(() => {
    initGame();

    if (canvasRef.value) {
        canvasRef.value.addEventListener('mousedown', handleMouseDown);
        canvasRef.value.addEventListener('mousemove', handleMouseMove);
        canvasRef.value.addEventListener('mouseup', handleMouseUp);
    }
});

onUnmounted(() => {
    if (canvasRef.value) {
        canvasRef.value.removeEventListener('mousedown', handleMouseDown);
        canvasRef.value.removeEventListener('mousemove', handleMouseMove);
        canvasRef.value.removeEventListener('mouseup', handleMouseUp);
    }

    if (render) {
        Matter.Render.stop(render);
        Matter.Runner.stop(runner);
    }
});
</script>

<template>
    <Head title="Le Ball - Sandbox" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-900 to-purple-900 flex flex-col items-center justify-center p-8">
        <div class="text-center mb-6">
            <h1 class="text-5xl font-bold text-white mb-2">🎯 Le Ball</h1>
            <p class="text-purple-200 text-lg">
                Drag the shapes to guide the ball into the bucket!
            </p>
        </div>

        <div class="relative mb-6">
            <canvas 
                ref="canvasRef" 
                class="border-4 border-purple-400 rounded-lg shadow-2xl bg-white"
                style="cursor: grab;"
            />
            
            <div 
                v-if="showCelebration" 
                class="absolute inset-0 flex items-center justify-center bg-black/50 rounded-lg"
            >
                <div class="text-center animate-bounce">
                    <div class="text-8xl mb-4">🎉 🎊 🎈</div>
                    <div class="text-5xl font-bold text-white mb-2">Congratulations!</div>
                    <div class="text-2xl text-purple-300">You did it! 🏆</div>
                </div>
            </div>
        </div>

        <div class="flex gap-4">
            <button
                @click="releaseBall"
                :disabled="gameStarted"
                class="px-8 py-3 bg-green-500 hover:bg-green-600 disabled:bg-gray-500 disabled:cursor-not-allowed text-white font-bold text-xl rounded-lg shadow-lg transition-all transform hover:scale-105"
            >
                {{ gameStarted ? '🎈 Ball Released!' : '🚀 Release Ball' }}
            </button>

            <button
                @click="resetGame"
                class="px-8 py-3 bg-purple-500 hover:bg-purple-600 text-white font-bold text-xl rounded-lg shadow-lg transition-all transform hover:scale-105"
            >
                🔄 Reset Game
            </button>
        </div>

        <div class="mt-6 text-center text-purple-200 max-w-2xl">
            <p class="text-sm">
                💡 <strong>Instructions:</strong> Drag the gray shapes onto the game board below the dashed line. 
                Position them so the ball will bounce and roll into the target bucket in the bottom-right corner. 
                Click "Release Ball" when ready!
            </p>
        </div>
    </div>
</template>
