<script setup lang="ts">
import { dashboard, login, register, clock, survey, ball, puzzle, snakedo, jokes, aStar, leBall, plinko, yoMommaBattle } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import DoNotPressButton from '@/components/DoNotPressButton.vue';

interface ToyItem {
    route: string;
    emoji: string;
    title: string;
    description: string;
    color: string;
    rotation: string;
}

const toys: ToyItem[] = [
    {
        route: clock(),
        emoji: '🕐',
        title: 'Time Machine',
        description: 'Watch time flow in real-time with animations!',
        color: 'from-blue-500 to-cyan-400',
        rotation: 'hover:rotate-12'
    },
    {
        route: ball(),
        emoji: '⚽',
        title: 'Spinning Ball',
        description: 'A mesmerizing 3D rotating sphere of chaos!',
        color: 'from-purple-500 to-pink-400',
        rotation: 'hover:-rotate-12'
    },
    {
        route: puzzle(),
        emoji: '🧩',
        title: 'Japanese Puzzle',
        description: '15-Puzzle with A* optimal solver! 数字パズル',
        color: 'from-pink-500 to-red-400',
        rotation: 'hover:rotate-6'
    },
    {
        route: snakedo(),
        emoji: '🐍',
        title: 'Voodoo Snake',
        description: 'Collect numbers in order without crossing yourself!',
        color: 'from-yellow-500 to-orange-400',
        rotation: 'hover:-rotate-6'
    },
    {
        route: aStar(),
        emoji: '🤖',
        title: 'Robot Pathfinder',
        description: 'A* algorithm visualization - watch robots navigate!',
        color: 'from-cyan-500 to-blue-400',
        rotation: 'hover:rotate-12'
    },
    {
        route: leBall(),
        emoji: '🎯',
        title: 'Le Ball',
        description: 'Physics puzzle! Guide the ball to the bucket!',
        color: 'from-indigo-500 to-purple-400',
        rotation: 'hover:-rotate-6'
    },
    {
        route: survey(),
        emoji: '⭐',
        title: 'Star Wars Survey',
        description: 'Which character are you? Take the survey!',
        color: 'from-red-500 to-yellow-400',
        rotation: 'hover:-rotate-12'
    },
    {
        route: jokes(),
        emoji: '😂',
        title: "Omar's Jokes",
        description: 'Dad jokes so bad they\'re good!',
        color: 'from-green-500 to-teal-400',
        rotation: 'hover:rotate-6'
    },
    {
        route: plinko(),
        emoji: '💰',
        title: 'Plinko',
        description: 'Drop the chip and win big! Price is Right style!',
        color: 'from-amber-500 to-yellow-400',
        rotation: 'hover:rotate-12'
    },
    {
        route: yoMommaBattle(),
        emoji: '🎤',
        title: 'AI Yo Momma Battle',
        description: 'Watch AI models roast each other with sick burns!',
        color: 'from-red-500 to-orange-500',
        rotation: 'hover:-rotate-12'
    }
];
</script>

<template>
    <Head title="Robert's Sandbox of Toys!" />
    
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden">
        <!-- Floating Background Elements -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-10 left-10 text-yellow-300 opacity-20 text-9xl animate-spin" style="animation-duration: 20s;">⚗️</div>
            <div class="absolute top-1/4 right-20 text-purple-300 opacity-20 text-7xl animate-bounce">🔬</div>
            <div class="absolute bottom-20 left-1/4 text-cyan-300 opacity-20 text-8xl animate-pulse">🧪</div>
            <div class="absolute bottom-40 right-1/3 text-pink-300 opacity-20 text-6xl animate-spin" style="animation-duration: 15s;">⚛️</div>
            <div class="absolute top-1/2 left-1/2 text-green-300 opacity-10 text-9xl animate-pulse">🧬</div>
        </div>

        <!-- Auth Links (top right) -->
        <div class="absolute top-6 right-6 z-20 flex gap-3">
            <Link
                v-if="$page.props.auth.user"
                :href="dashboard()"
                class="px-4 py-2 rounded-lg bg-purple-500/20 border-2 border-purple-400 text-purple-200 hover:bg-purple-500/40 hover:border-purple-300 transition-all duration-300"
            >
                🔐 Dashboard
            </Link>
            <Link
                v-else
                :href="login()"
                class="px-4 py-2 rounded-lg bg-cyan-500/20 border-2 border-cyan-400 text-cyan-200 hover:bg-cyan-500/40 hover:border-cyan-300 transition-all duration-300"
            >
                🔑 Log In
            </Link>
            <Link
                v-if="!$page.props.auth.user"
                :href="register()"
                class="px-4 py-2 rounded-lg bg-pink-500/20 border-2 border-pink-400 text-pink-200 hover:bg-pink-500/40 hover:border-pink-300 transition-all duration-300"
            >
                ✨ Register
            </Link>
        </div>

        <!-- Main Content -->
        <div class="container mx-auto px-4 py-12 relative z-10">
            <!-- Mad Scientist Header -->
            <div class="text-center mb-16">
                <div class="flex justify-center items-center gap-4 mb-6">
                    <span class="text-7xl animate-bounce" style="animation-duration: 2s;">🧪</span>
                    <span class="text-7xl animate-spin" style="animation-duration: 3s;">⚗️</span>
                    <span class="text-7xl animate-pulse">🔬</span>
                </div>
                
                <h1 class="text-6xl md:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-pink-500 to-cyan-400 mb-4 animate-pulse tracking-tight">
                    Robert's Sandbox of Toys!
                </h1>
                
                <p class="text-2xl text-purple-300 font-mono italic mb-2">
                    🧬 Where Science Meets Chaos! 💥
                </p>
                <p class="text-lg text-cyan-300 font-mono">
                    ⚠️ WARNING: May cause excessive fun and learning ⚠️
                </p>
            </div>

            <!-- Chaotic Grid of Experiments -->
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <Link
                    v-for="(toy, index) in toys"
                    :key="index"
                    :href="toy.route"
                    class="group relative"
                >
                    <div 
                        :class="[
                            'h-full p-6 rounded-2xl border-4 transition-all duration-500 transform hover:scale-110 hover:z-10',
                            'bg-gradient-to-br backdrop-blur-sm shadow-2xl',
                            'border-white/30 hover:border-white/60',
                            'cursor-pointer',
                            toy.rotation,
                            `bg-gradient-to-br ${toy.color}`
                        ]"
                    >
                        <!-- Emoji Badge -->
                        <div class="text-7xl mb-4 transform group-hover:scale-125 transition-transform duration-300">
                            {{ toy.emoji }}
                        </div>
                        
                        <!-- Title -->
                        <h3 class="text-2xl font-black text-white mb-2 drop-shadow-lg">
                            {{ toy.title }}
                        </h3>
                        
                        <!-- Description -->
                        <p class="text-white/90 font-mono text-sm leading-relaxed">
                            {{ toy.description }}
                        </p>
                        
                        <!-- Hover Effect Overlay -->
                        <div class="absolute inset-0 bg-white/0 group-hover:bg-white/10 rounded-2xl transition-all duration-300 pointer-events-none"></div>
                        
                        <!-- Corner Decoration -->
                        <div class="absolute top-2 right-2 text-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 animate-spin" style="animation-duration: 2s;">
                            ⚡
                        </div>
                    </div>
                </Link>
                <DoNotPressButton />
            </div>

            <!-- Footer Lab Notes -->
            <div class="max-w-4xl mx-auto mt-16 p-8 bg-black/40 backdrop-blur-md rounded-xl border-4 border-dashed border-yellow-400/50">
                <div class="flex items-start gap-4">
                    <span class="text-5xl">📝</span>
                    <div>
                        <h2 class="text-2xl font-bold text-yellow-300 mb-3 font-mono">Lab Notes:</h2>
                        <ul class="space-y-2 text-cyan-200 font-mono text-sm">
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400">→</span>
                                <span>Built with Laravel 12, Vue 3, TypeScript & Inertia.js</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400">→</span>
                                <span>Each experiment is a unique blend of code, creativity & chaos!</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400">→</span>
                                <span>No lab rats were harmed in the making of these toys... probably 🐭</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-pink-400">→</span>
                                <span>Side effects may include: joy, curiosity, and an urge to build things!</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
