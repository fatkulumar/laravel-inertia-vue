<script setup>
import AuthenticatedLayoutCommittee from '@/Layouts/AuthenticatedLayoutCommittee.vue';
import { Head } from '@inertiajs/vue3';
import { initFlowbite } from 'flowbite';
import { onMounted } from 'vue';

onMounted(() => {
    initFlowbite();
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayoutCommittee>
        <!-- <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template> -->

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">You're logged in Panitia!</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayoutCommittee>
</template>
