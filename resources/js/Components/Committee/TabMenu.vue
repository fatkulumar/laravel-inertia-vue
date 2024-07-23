<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  id: {
    type: String,
    default: () => ({}),
  },
});

const urlPath = window.location.pathname;
const segments = urlPath.split("/");
const idSubmissionLastSegment = segments.pop() || segments.pop();
</script>

<template>
  <div class="flex flex-wrap gap-2">
    <Link :href="`/committee/schedule/detail/${id}`">
      <h2
        class="font-semibold leading-tight border-1 border-black px-5 py-1 rounded-md"
        :class="{
          'bg-blue-700 text-white': route().current(
            'committee.schedule.detail*'
          ),
        }"
      >
        Detail Jadwal
      </h2>
    </Link>
    <Link :href="`/committee/participant/list-participant`">
      <h2
        class="font-semibold leading-tight border-1 border-black px-5 py-1 rounded-md"
        :class="{
          'bg-blue-700 text-white': route().current('committee.participant*'),
        }"
      >
        Data Peserta
      </h2>
    </Link>
  </div>
</template>
