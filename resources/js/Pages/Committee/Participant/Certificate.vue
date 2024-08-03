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
  certificate: {
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
      <template #headerTitle> Syahadah </template>
      <template #header>
        <TabMenuDetailParticipant :id="idSubmissionLastSegment" />
      </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                {{ certificate }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayoutCommittee>
  </div>
</template>
