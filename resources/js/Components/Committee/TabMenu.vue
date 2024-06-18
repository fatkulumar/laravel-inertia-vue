<script setup lang="ts">
    import { Link } from '@inertiajs/vue3';

    const urlPath = window.location.pathname;
    const segments = urlPath.split('/');
    const idSubmissionLastSegment = segments.pop() || segments.pop();
</script>

<template>
    <div class="flex flex-wrap gap-2">
        <Link :href="`/committee/schedule/detail/${idSubmissionLastSegment}`">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight border-2 border-black px-5 py-1">Detail Jadwal</h2>
        </Link>
        <Link :href="`/committee/participant/${idSubmissionLastSegment}`">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight border-2 border-black px-5 py-1">Data Peserta</h2>
        </Link>
    </div>
</template>
