<script setup lang="ts">
import EclHeader from '@/components/ecl/EclHeader.vue';
import EclFooter from '@/components/ecl/EclFooter.vue';
</script>

<template>
    <EclHeader />

    <!-- Page Content (slot for page-specific content) -->
    <slot />

    <EclFooter />
</template>
