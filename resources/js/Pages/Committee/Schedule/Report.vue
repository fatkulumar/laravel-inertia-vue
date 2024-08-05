<script setup>
import AuthenticatedLayoutCommittee from "@/Layouts/AuthenticatedLayoutCommittee.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { computed, onMounted, ref, watch } from "vue";
// import Pagination from "@/Components/Partials/Pagination.vue";
import Swal from "sweetalert2";
// import { Modal } from "flowbite";
import TabMenuDetailSchedule from "@/Components/Committee/TabMenuDetailSchedule.vue";

onMounted(() => {
  initFlowbite();
});

const props = defineProps({
  reports: {
    type: Object,
    default: () => ({}),
  },
  appointmentFiles: {
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

const form = useForm({
  id: props.appointmentFiles[0]?.id ? props.appointmentFiles[0]?.id : "",
  schedule_id: idSubmissionLastSegment,
  name: "",
  file: "",
});

function resetForm() {
  form.id = props.appointmentFiles[0]?.id ? props.appointmentFiles[0]?.id : "";
  form.name = "";
  form.file = "";
}

function toast(icon = "success", text = "Data Berhasil Disimpan") {
  const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer;
      toast.onmouseleave = Swal.resumeTimer;
    },
  });
  Toast.fire({
    icon: icon,
    title: text,
  });
}

function uploadFile(e) {
  const file = e.target.files[0];
  if (
      (file.type == "application/pdf")
    ) {
      form.file = file;
      form.name = file.name;
  } else {
    form.file = null;
    form.name = null;
    toast("warning", "Harus Format Gambar");
  }
}

function addAppointmentFile() {
  form.post("/committee/schedule/upload-appointment-file", {
    preserveScroll: true,
    onSuccess: () => {
      resetForm();
      toast("success", "Berhasil");
    },
  });
}

</script>

<template>
  <Head title="Laporan" />
  <div>
    <AuthenticatedLayoutCommittee>
      <template #header>
        <TabMenuDetailSchedule :id="idSubmissionLastSegment" />
      </template>
      <template #headerTitle>
        Laporan
      </template>
      <div class="md:py-12 mx-auto sm:px-6 lg:px-8">
        <div
          class="m-6 text-gray-700 bg-clip-border rounded-xl flex flex-col md:grid md:grid-cols-3 gap-2 md:gap-12"
        >
          <div class="bg-white shadow-lg">
            <!-- total peserta -->
            <div class="flex justify-end">
              <div
                title="Actions"
                class="w-5 cursor-pointer"
                id="dropdown-button"
                :data-dropdown-toggle="`dropdownParticipant`"
              >
                <svg
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
                      d="M12 12H12.01M12 6H12.01M12 18H12.01M13 12C13 12.5523 12.5523 13 12 13C11.4477 13 11 12.5523 11 12C11 11.4477 11.4477 11 12 11C12.5523 11 13 11.4477 13 12ZM13 18C13 18.5523 12.5523 19 12 19C11.4477 19 11 18.5523 11 18C11 17.4477 11.4477 17 12 17C12.5523 17 13 17.4477 13 18ZM13 6C13 6.55228 12.5523 7 12 7C11.4477 7 11 6.55228 11 6C11 5.44772 11.4477 5 12 5C12.5523 5 13 5.44772 13 6Z"
                      stroke="#000000"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    ></path>
                  </g>
                </svg>
              </div>
              <div
                :id="`dropdownParticipant`"
                class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700"
              >
                <ul
                  class="py-2 text-sm text-gray-700 dark:text-gray-200"
                  aria-labelledby="dropdown-button"
                >
                  <li>
                    <button
                      title="Download Data Total Peserta"
                      type="button"
                      class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                    >
                      Download Data Total Peserta
                    </button>
                  </li>
                  <li>
                    <button
                      title="Download Data Laki Laki"
                      type="button"
                      class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                    >
                      Download Data Laki Laki
                    </button>
                  </li>
                  <li>
                    <button
                      title="Download Data Perempuan"
                      type="button"
                      class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                    >
                      Download Data Perempuan
                    </button>
                  </li>
                </ul>
              </div>
            </div>
            <div class="px-6 pb-6">
              <table width="100%">
                <thead>
                  <tr class="">
                    <th class="text-left">Total Peserta</th>
                    <td class="">{{ props.reports?.participants?.total }}</td>
                  </tr>
                  <tr>
                    <th class="text-left">Laki Laki</th>
                    <td>{{ props.reports?.participants?.male }}</td>
                  </tr>

                  <tr>
                    <th class="text-left">Perempaun</th>
                    <td>{{ props.reports?.participants?.female }}</td>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
          <!-- total peserta lulus -->
          <div class="bg-white shadow-lg">
            <div class="flex justify-end">
              <div
                title="Actions"
                class="w-5 cursor-pointer"
                id="dropdown-button"
                :data-dropdown-toggle="`dropdownGraduatedParticipant`"
              >
                <svg
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
                      d="M12 12H12.01M12 6H12.01M12 18H12.01M13 12C13 12.5523 12.5523 13 12 13C11.4477 13 11 12.5523 11 12C11 11.4477 11.4477 11 12 11C12.5523 11 13 11.4477 13 12ZM13 18C13 18.5523 12.5523 19 12 19C11.4477 19 11 18.5523 11 18C11 17.4477 11.4477 17 12 17C12.5523 17 13 17.4477 13 18ZM13 6C13 6.55228 12.5523 7 12 7C11.4477 7 11 6.55228 11 6C11 5.44772 11.4477 5 12 5C12.5523 5 13 5.44772 13 6Z"
                      stroke="#000000"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    ></path>
                  </g>
                </svg>
              </div>
              <div
                :id="`dropdownGraduatedParticipant`"
                class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700"
              >
                <ul
                  class="py-2 text-sm text-gray-700 dark:text-gray-200"
                  aria-labelledby="dropdown-button"
                >
                  <li>
                    <button
                      title="Download Total Peserta Lulus"
                      type="button"
                      class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                    >
                      Download Data Total Peserta Lulus
                    </button>
                  </li>
                  <li>
                    <button
                      title="Download Data Laki Laki Lulus"
                      type="button"
                      class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                    >
                      Download Data Laki Laki Lulus
                    </button>
                  </li>
                  <li>
                    <button
                      title="Download Data Perempuan Lulus"
                      type="button"
                      class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                    >
                      Download Data Perempuan Lulus
                    </button>
                  </li>
                </ul>
              </div>
            </div>
            <div class="px-6 pb-6">
              <table width="100%">
                <thead>
                  <tr>
                    <th class="text-left">Total Peserta Lulus</th>
                    <td class="">
                      {{ props.reports?.participant_graduated?.total }}
                    </td>
                  </tr>
                  <tr>
                    <th class="text-left">Laki Laki</th>
                    <td>{{ props.reports?.participant_graduated?.male }}</td>
                  </tr>

                  <tr>
                    <th class="text-left">Perempaun</th>
                    <td>{{ props.reports?.participant_graduated?.female }}</td>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

          <div class="bg-white shadow-lg">
            <div class="flex justify-end">
              <div
                title="Actions"
                class="w-5 cursor-pointer"
                id="dropdown-button"
                :data-dropdown-toggle="`dropdownNotGraduatedParticipant`"
              >
                <svg
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
                      d="M12 12H12.01M12 6H12.01M12 18H12.01M13 12C13 12.5523 12.5523 13 12 13C11.4477 13 11 12.5523 11 12C11 11.4477 11.4477 11 12 11C12.5523 11 13 11.4477 13 12ZM13 18C13 18.5523 12.5523 19 12 19C11.4477 19 11 18.5523 11 18C11 17.4477 11.4477 17 12 17C12.5523 17 13 17.4477 13 18ZM13 6C13 6.55228 12.5523 7 12 7C11.4477 7 11 6.55228 11 6C11 5.44772 11.4477 5 12 5C12.5523 5 13 5.44772 13 6Z"
                      stroke="#000000"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                    ></path>
                  </g>
                </svg>
              </div>
              <div
                :id="`dropdownNotGraduatedParticipant`"
                class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700"
              >
                <ul
                  class="py-2 text-sm text-gray-700 dark:text-gray-200"
                  aria-labelledby="dropdown-button"
                >
                  <li>
                    <button
                      title="Download Data Total Tidak Lulus"
                      type="button"
                      class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                    >
                      Download Data Total Peserta Tidak Lulus
                    </button>
                  </li>
                  <li>
                    <button
                      title="Download Data Laki Laki Tidak Lulus"
                      type="button"
                      class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                    >
                      Download Data Laki Laki Tidak Lulus
                    </button>
                  </li>
                  <li>
                    <button
                      title="Download Data Perempuan Tidak Lulus"
                      type="button"
                      class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                    >
                      Download Data Perempuan Tidak Lulus
                    </button>
                  </li>
                </ul>
              </div>
            </div>
            <div class="px-6 pb-6">
              <table width="100%">
                <thead>
                  <tr>
                    <th class="text-left">Total Peserta Tidak Lulus</th>
                    <td class="">
                      {{ props.reports?.participant_not_graduated?.total }}
                    </td>
                  </tr>
                  <tr>
                    <th class="text-left">Laki Laki</th>
                    <td>
                      {{ props.reports?.participant_not_graduated?.male }}
                    </td>
                  </tr>

                  <tr>
                    <th class="text-left">Perempaun</th>
                    <td>
                      {{ props.reports?.participant_not_graduated?.female }}
                    </td>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>

        <div
          class=" text-gray-700 bg-clip-border rounded-xl"
        >
        <div class="text-red-600 text-sm ml-2" v-for="error, index in props.errors" :key="index">
            *{{ error }}
        </div>
          <div class="bg-white shadow-lg">
            <label for="apoointment_file" class="mx-auto flex justify-center">Berita Acara Baiat</label>
            <div class="bg-gray-100 flex items-center mb-2" v-if="props.appointmentFiles">
                <div class="w-1/12 bg-gray-200 flex">
                    <p class="w-1/12 p-6 text-red-500 font-bold text-lg">
                        PDF
                    </p>
                </div>
                <div class="ml-2">
                    <a class="hover:underline hover:text-blue-500" :href="props.appointmentFiles[0]?.name" target="_blank" rel="noopener noreferrer">{{ props.appointmentFiles[0]?.name }}</a>
                </div>
            </div>
            <form @submit.prevent="addAppointmentFile" enctype="multipart/form-data">
                <input @change="uploadFile" required accept="application/pdf" type="file" name="apoointment_file" class="w-full">
                <button type="submit" class="w-full p-2 bg-blue-500 text-white md:rounded-lg">Upload</button>
            </form>
          </div>
        </div>
      </div>
    </AuthenticatedLayoutCommittee>
  </div>
</template>
