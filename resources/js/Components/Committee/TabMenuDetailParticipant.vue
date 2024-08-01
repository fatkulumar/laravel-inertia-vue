<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import { onMounted } from "vue";

onMounted(() => {
    initFlowbite();
});

// id_participant
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
       <!-- mobile -->
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
             :href="`/committee/participant/detail/${id}`"
              class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
              >Data Peserta</Link
            >
          </li>
          <li>
            <Link replace
             :href="`/committee/participant/speaker-list/${id}`"
              class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
              >Riwayat Peserta</Link
            >
          </li>
          <li>
            <Link replace
             :href="`/committee/participant/letter/${id}`"
              class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
              >Syahadah</Link
            >
          </li>

        </ul>
      </div>
    </div>

    <!-- desktop -->
    <div class="md:flex flex-wrap gap-2 hidden">
      <Link replace
        class="ring-2 rounded-md"
        :href="`/committee/participant/detail/${id}`"
      >
        <h2
          class="font-semibold leading-tight border-1 border-black px-5 py-1 rounded-md"
          :class="{
            'bg-blue-700 text-white': route().current(
              'committee.participant.detail*'
            ),
          }"
        >
          Data Peserta
        </h2>
      </Link>

      <Link replace
        class="ring-2 rounded-md"
        :href="`/committee/participant/history-class/${id}`"
      >
        <h2
          class="font-semibold leading-tight border-1 border-black px-5 py-1 rounded-md"
          :class="{
            'bg-blue-700 text-white': route().current(
              'committee.participant.history.class*'
            ),
          }"
        >
          Riwayat Kelas
        </h2>
      </Link>

      <Link replace
        class="ring-2 rounded-md"
        :href="`/committee/participant/letter/${id}`"
      >
        <h2
          class="font-semibold leading-tight border-1 border-black px-5 py-1 rounded-md"
          :class="{
            'bg-blue-700 text-white': route().current(
              'committee.participant.letter*'
            ),
          }"
        >
        Syahadah
        </h2>
      </Link>
    </div>
  </div>
</template>
