<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

interface LLMModel {
    name: string;
    slug: string;
}

interface Props {
    llmModels: LLMModel[];
}

interface JokeDisplay {
    id: number;
    text: string;
    model: LLMModel;
    side: 'left' | 'right';
    showRating: boolean;
    hoveredStar: number | null;
}

const props = defineProps<Props>();

const leftModel = ref<LLMModel | null>(null);
const rightModel = ref<LLMModel | null>(null);
const jokes = ref<JokeDisplay[]>([]);
const battleInterval = ref<number | null>(null);
const isLeftTurn = ref(true);

const getRandomModel = (): LLMModel => {
    return props.llmModels[Math.floor(Math.random() * props.llmModels.length)];
};

const fetchRandomJoke = async (): Promise<{ id: number; joke: string } | null> => {
    try {
        const response = await axios.get('/yo-momma-battle/random-joke');
        return response.data;
    } catch (error) {
        console.error('Failed to fetch joke:', error);
        return null;
    }
};

const rateJoke = async (jokeId: number, modelSlug: string, rating: number) => {
    try {
        await axios.post('/yo-momma-battle/rate', {
            joke_id: jokeId,
            llm_model_slug: modelSlug,
            rating: rating,
        });
    } catch (error) {
        console.error('Failed to rate joke:', error);
    }
};

const handleRating = async (joke: JokeDisplay, rating: number) => {
    await rateJoke(joke.id, joke.model.slug, rating);
    joke.showRating = false;
};

const tellJoke = async () => {
    const jokeData = await fetchRandomJoke();
    if (!jokeData) {
        return;
    }

    const side = isLeftTurn.value ? 'left' : 'right';
    const model = side === 'left' ? leftModel.value : rightModel.value;

    if (!model) {
        return;
    }

    const newJoke: JokeDisplay = {
        id: jokeData.id,
        text: jokeData.joke,
        model: model,
        side: side,
        showRating: true,
        hoveredStar: null,
    };

    jokes.value.push(newJoke);

    if (jokes.value.length > 10) {
        jokes.value.shift();
    }

    isLeftTurn.value = !isLeftTurn.value;
};

const startBattle = () => {
    leftModel.value = getRandomModel();
    rightModel.value = getRandomModel();

    while (rightModel.value.slug === leftModel.value.slug) {
        rightModel.value = getRandomModel();
    }

    tellJoke();

    let counter = 0;
    battleInterval.value = window.setInterval(() => {
        counter++;
        if (counter % 3 === 0) {
            tellJoke();
        }
    }, 10000);
};

onMounted(() => {
    startBattle();
});

onUnmounted(() => {
    if (battleInterval.value !== null) {
        clearInterval(battleInterval.value);
    }
});
</script>

<template>
    <Head title="AI Yo Momma Battle Royale - Sandbox" />

    <div class="min-h-screen bg-zinc-950 text-white overflow-hidden font-bold">
        <!-- Graffiti Background -->
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <div class="absolute top-10 left-10 text-9xl rotate-12 text-red-500">💀</div>
            <div class="absolute bottom-20 right-20 text-9xl -rotate-12 text-blue-500">💯</div>
            <div class="absolute top-1/2 left-1/4 text-6xl text-yellow-500">🔥</div>
            <div class="absolute top-1/3 right-1/3 text-6xl text-purple-500">💥</div>
        </div>

        <!-- Title Bar -->
        <div class="relative z-10 bg-gradient-to-r from-red-900/80 via-purple-900/80 to-blue-900/80 border-b-4 border-yellow-400 shadow-2xl">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <h1 class="text-5xl md:text-7xl font-black text-center uppercase tracking-wider">
                    <span class="text-red-500 drop-shadow-[0_0_15px_rgba(239,68,68,0.8)]">AI</span>
                    <span class="text-yellow-400 mx-2 drop-shadow-[0_0_15px_rgba(250,204,21,0.8)]">YO MOMMA</span>
                    <span class="text-blue-500 drop-shadow-[0_0_15px_rgba(59,130,246,0.8)]">BATTLE</span>
                </h1>
                <p class="text-center text-xl text-gray-300 mt-2 uppercase tracking-widest">🎤 Royale 🎤</p>
            </div>
        </div>

        <!-- Battle Arena -->
        <div class="relative z-10 grid grid-cols-2 gap-0 min-h-[calc(100vh-180px)]">
            <!-- RED SIDE (LEFT) -->
            <div class="bg-gradient-to-br from-red-950 via-red-900 to-red-950 border-r-4 border-yellow-400 p-8 flex flex-col">
                <!-- Model Display -->
                <div class="mb-8 text-center">
                    <div class="inline-block bg-black/50 border-4 border-red-500 rounded-lg px-6 py-4 shadow-2xl">
                        <p class="text-sm uppercase tracking-wider text-red-300">Red Corner</p>
                        <p class="text-3xl font-black text-red-400 mt-1 drop-shadow-[0_0_10px_rgba(248,113,113,0.8)]">
                            {{ leftModel?.name || 'Loading...' }}
                        </p>
                    </div>
                </div>

                <!-- Jokes Display -->
                <div class="flex-1 space-y-6 overflow-y-auto">
                    <div
                        v-for="(joke, index) in jokes.filter(j => j.side === 'left')"
                        :key="index"
                        class="animate-in fade-in slide-in-from-left-10 duration-500"
                    >
                        <!-- Speech Bubble -->
                        <div class="relative bg-red-600 rounded-3xl p-6 shadow-2xl border-4 border-red-800 max-w-md ml-4">
                            <p class="text-xl text-white font-bold leading-relaxed">{{ joke.text }}</p>
                        </div>

                        <!-- Star Rating -->
                        <div v-if="joke.showRating" class="mt-3 ml-6">
                            <p class="text-xs text-red-200 mb-1 uppercase tracking-wide">Rate this joke (1=worst, 5=best)</p>
                            <div class="flex gap-2">
                                <button
                                    v-for="star in 5"
                                    :key="star"
                                    @click="handleRating(joke, star)"
                                    @mouseenter="joke.hoveredStar = star"
                                    @mouseleave="joke.hoveredStar = null"
                                    class="text-3xl hover:scale-125 transition-transform duration-200 filter drop-shadow-lg w-10 h-10 flex items-center justify-center"
                                >
                                    {{ joke.hoveredStar && star <= joke.hoveredStar ? '⭐' : '☆' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BLUE SIDE (RIGHT) -->
            <div class="bg-gradient-to-br from-blue-950 via-blue-900 to-blue-950 p-8 flex flex-col">
                <!-- Model Display -->
                <div class="mb-8 text-center">
                    <div class="inline-block bg-black/50 border-4 border-blue-500 rounded-lg px-6 py-4 shadow-2xl">
                        <p class="text-sm uppercase tracking-wider text-blue-300">Blue Corner</p>
                        <p class="text-3xl font-black text-blue-400 mt-1 drop-shadow-[0_0_10px_rgba(96,165,250,0.8)]">
                            {{ rightModel?.name || 'Loading...' }}
                        </p>
                    </div>
                </div>

                <!-- Jokes Display -->
                <div class="flex-1 space-y-6 overflow-y-auto flex flex-col items-end">
                    <div
                        v-for="(joke, index) in jokes.filter(j => j.side === 'right')"
                        :key="index"
                        class="animate-in fade-in slide-in-from-right-10 duration-500 w-full flex flex-col items-end"
                    >
                        <!-- Speech Bubble -->
                        <div class="relative bg-blue-600 rounded-3xl p-6 shadow-2xl border-4 border-blue-800 max-w-md mr-4">
                            <p class="text-xl text-white font-bold leading-relaxed">{{ joke.text }}</p>
                        </div>

                        <!-- Star Rating -->
                        <div v-if="joke.showRating" class="mt-3 mr-6">
                            <p class="text-xs text-blue-200 mb-1 uppercase tracking-wide text-right">Rate this joke (1=worst, 5=best)</p>
                            <div class="flex gap-2">
                                <button
                                    v-for="star in 5"
                                    :key="star"
                                    @click="handleRating(joke, star)"
                                    @mouseenter="joke.hoveredStar = star"
                                    @mouseleave="joke.hoveredStar = null"
                                    class="text-3xl hover:scale-125 transition-transform duration-200 filter drop-shadow-lg w-10 h-10 flex items-center justify-center"
                                >
                                    {{ joke.hoveredStar && star <= joke.hoveredStar ? '⭐' : '☆' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="relative z-10 bg-gradient-to-r from-red-900/80 via-purple-900/80 to-blue-900/80 border-t-4 border-yellow-400 py-4">
            <p class="text-center text-gray-300 uppercase tracking-widest">
                🔥 Battle Never Stops 🔥
            </p>
        </div>
    </div>
</template>

<style scoped>
@keyframes slide-in-from-left-10 {
    from {
        transform: translateX(-2.5rem);
    }
}

@keyframes slide-in-from-right-10 {
    from {
        transform: translateX(2.5rem);
    }
}

.animate-in {
    animation-fill-mode: both;
}

.fade-in {
    animation-name: fadeIn;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.slide-in-from-left-10 {
    animation-name: slide-in-from-left-10;
}

.slide-in-from-right-10 {
    animation-name: slide-in-from-right-10;
}

.duration-500 {
    animation-duration: 500ms;
}
</style>
