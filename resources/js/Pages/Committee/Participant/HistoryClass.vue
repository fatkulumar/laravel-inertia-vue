<script setup>
import AuthenticatedLayoutCommittee from "@/Layouts/AuthenticatedLayoutCommittee.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { Modal } from "flowbite";
import Swal from "sweetalert2";
import TabMenuDetailParticipant from "@/Components/Committee/TabMenuDetailParticipant.vue";

onMounted(() => {
  initFlowbite();
});

const props = defineProps({
  schedules: {
    type: Object,
    default: () => ({}),
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
});

const urlPath = window.location.pathname;
const segments = urlPath.split("/");
const idSubmissionLastSegment = segments.pop() || segments.pop();
</script>

<template>
  <Head title="Event Tersedia" />
  <div>
    <AuthenticatedLayoutCommittee>
      <template #headerTitle> Riwayat Kelas </template>
      <template #header>
        <TabMenuDetailParticipant :id="idSubmissionLastSegment" />
      </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <div v-if="props.schedules.length < 1">
                  <p class="text-center">Tidak Ada Kelas</p>
                </div>
                <div
                  v-else
                  class="flex gap-2 items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900"
                  v-for="(item, index) in props.schedules"
                  :key="index"
                >
                  <div
                    v-for="(submission, indexSubmission) in item.submissions"
                    :key="indexSubmission"
                    class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 w-full"
                  >
                    <img
                      class="object-cover w-full rounded-t-lg h-96 md:h-auto md:w-48 md:rounded-none md:rounded-s-lg"
                      :src="item.poster"
                      alt=""
                    />
                    <div class="flex justify-between items-center">
                      <div class="flex flex-col justify-between leading-normal">
                        <div class="flex">
                          <h5
                            class="tracking-tight text-gray-900 dark:text-white"
                          >
                            <span class="font-bold"
                              >{{ item.class_room?.name }} |
                              {{ item.category?.name }}</span
                            >
                            Angkatan Ke
                            <span class="font-bold">{{ item.periode }}</span>
                          </h5>
                        </div>

                        <div class="flex gap-2 rounded-sm flex-col">
                          <p>{{ submission.updated_at }}</p>
                        </div>
                      </div>
                      <div class="flex flex-col gap-1">
                        <div
                          class="rounded-md text-white text-center px-1"
                          :class="{
                            'bg-green-500': submission.status == 'graduated',
                            'bg-yellow-500': submission.status != 'graduated',
                          }"
                        >
                          {{ submission.status }}
                        </div>

                        <Link :href="`/committee/participant/certificate/${submission.participant_id}`"
                          v-if="submission.status == 'graduated'"
                          class="rounded-md text-white px-1 bg-orange-500 hover:bg-orange-600"
                        >
                          Lihat Syahadah
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayoutCommittee>
  </div>
</template>
