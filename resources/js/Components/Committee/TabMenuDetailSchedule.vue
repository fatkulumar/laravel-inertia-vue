<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import { onMounted } from "vue";

onMounted(() => {
    initFlowbite();
});

// id_schedule
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
  <div>
    <div
      class="md:hidden flex justify-center"
      id="dropdownDelayButton"
      data-dropdown-toggle="dropdownDelay"
      data-dropdown-delay="500"
      data-dropdown-trigger="hover"
    >
      <svg
        class="w-6 h-6"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g
          id="SVGRepo_tracerCarrier"
          stroke-linecap="round"
          stroke-linejoin="round"
        ></g>
        <g id="SVGRepo_iconCarrier">
          <path
            d="M5 6H12H19M5 12H19M5 18H19"
            stroke="#000000"
            stroke-width="2"
            stroke-linecap="round"
          ></path>
        </g>
      </svg>

      <!-- Dropdown menu -->
      <div
        id="dropdownDelay"
        class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700"
      >
        <ul
          class="py-2 text-sm text-gray-700 dark:text-gray-200"
          aria-labelledby="dropdownDelayButton"
        >
          <li>
            <Link replace
             :href="`/committee/schedule/detail/${id}`"
              class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
              >Detail Jadwal</Link
            >
          </li>
          <li>
            <Link replace
             :href="`/committee/schedule/speaker-list/${id}`"
              class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
              >Narasumber</Link
            >
          </li>
          <li>
            <Link replace
             :href="`/committee/schedule/letter/${id}`"
              class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
              >Surat Tugas</Link
            >
          </li>

          <li>
            <Link replace
            :href="`/committee/schedule/list-participant/${id}`"
              class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
              >Data Peserta</Link
            >
          </li>

          <li>
            <Link replace
              :href="`/committee/schedule/report/${id}`"
              class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
              >Laporan</Link
            >
          </li>

        </ul>
      </div>
    </div>

    <div class="md:flex flex-wrap gap-2 hidden">
      <Link replace
        class="ring-2 rounded-md"
        :href="`/committee/schedule/detail/${id}`"
      >
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

      <Link replace
        class="ring-2 rounded-md"
        :href="`/committee/schedule/speaker-list/${id}`"
      >
        <h2
          class="font-semibold leading-tight border-1 border-black px-5 py-1 rounded-md"
          :class="{
            'bg-blue-700 text-white': route().current(
              'committee.schedule.speaker*'
            ),
          }"
        >
          Narasumber
        </h2>
      </Link>

      <Link replace
        class="ring-2 rounded-md"
        :href="`/committee/schedule/letter/${id}`"
      >
        <h2
          class="font-semibold leading-tight border-1 border-black px-5 py-1 rounded-md"
          :class="{
            'bg-blue-700 text-white': route().current(
              'committee.schedule.letter*'
            ),
          }"
        >
          Surat Tugas
        </h2>
      </Link>

      <Link replace
        class="ring-2 rounded-md"
        :href="`/committee/schedule/list-participant/${id}`"
      >
        <h2
          class="font-semibold leading-tight border-1 border-black px-5 py-1 rounded-md"
          :class="{
            'bg-blue-700 text-white': route().current(
              'committee.schedule.participant*'
            ),
          }"
        >
          Data Peserta
        </h2>
      </Link>

      <Link replace
        class="ring-2 rounded-md"
        :href="`/committee/schedule/documentation/${id}`"
      >
        <h2
          class="font-semibold leading-tight border-1 border-black px-5 py-1 rounded-md"
          :class="{
            'bg-blue-700 text-white': route().current(
              'committee.schedule.documentation*'
            ),
          }"
        >
          Dokumentasi
        </h2>
      </Link>

      <Link replace
        class="ring-2 rounded-md"
        :href="`/committee/schedule/report/${id}`"
      >
        <h2
          class="font-semibold leading-tight border-1 border-black px-5 py-1 rounded-md"
          :class="{
            'bg-blue-700 text-white': route().current(
              'committee.schedule.report*'
            ),
          }"
        >
          Laporan
        </h2>
      </Link>
    </div>
  </div>
</template>
