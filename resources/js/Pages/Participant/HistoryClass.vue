<script setup>
import AuthenticatedLayoutParticipant from "@/Layouts/AuthenticatedLayoutParticipant.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { Modal } from "flowbite";
import Swal from "sweetalert2";
import TabMenuDetailParticipant from "@/Components/Participant/TabMenuDetailParticipant.vue";


onMounted(() => {
  initFlowbite();
});

const props = defineProps({
  histories: {
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

const form = useForm({
  schedule_id: "",
  proof: null,
});

let nameClassRoom = ref("");
let category = ref("");

const showModal = (
  targetModal = "submission-modal",
  cardId,
  cardNameClassRoom,
  cardCategory
) => {
  form.schedule_id = cardId;
  nameClassRoom.value = cardNameClassRoom;
  category.value = cardCategory;
  const $targetEl = document.getElementById(targetModal);
  const modal = new Modal($targetEl);
  modal.show();
};

const closeModal = (targetModal = "submission-modal") => {
  previewProof.value = null;
  const $targetEl = document.getElementById(targetModal);
  const modal = new Modal($targetEl);
  modal.hide();
};

const fileInput = ref(null);

const triggerFileInput = () => {
  fileInput.value?.click();
};

const previewProof = ref(null);
function handleFileUpload(e) {
  const image = e.target.files[0];
  if (
    (image.type == "image/png") |
    (image.type == "image/jpg") |
    (image.type == "image/jpeg")
  ) {
    const reader = new FileReader();
    reader.readAsDataURL(image);
    reader.onload = (e) => {
      previewProof.value = e.target.result;
      form.proof = image;
    };
  } else {
    toast("warning", "Harus Format Gambar");
  }
}

function toast(icon = "success", text = "Data Berhasil Ditambahkan") {
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

function hanldeRegisterClass() {
  form.post("/participant/participant/register-class", {
    preserveScroll: true,
    onSuccess: () => {
      form.previewProof = null;
      closeModal();
      toast("success", "Berhasil Daftar");
    },
  });
}
</script>

<template>
  <Head title="History Kelas" />
  <div>
    <AuthenticatedLayoutParticipant>
        <template #header>
          <TabMenuDetailParticipant />
        </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <div v-if="props.histories.length < 1">
                  <p class="text-center">Tidak Ada Kelas</p>
                </div>
                <div
                  v-else
                  class="flex gap-2 items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900"
                  v-for="(item, index) in props.histories"
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
                              >{{ submission.schedule.class_room.name }} |
                              {{ submission.schedule.category.name }}</span
                            >
                            <div>
                              Angkatan Ke
                              <span class="font-bold">{{
                                submission.schedule.periode
                              }}</span>
                            </div>
                          </h5>
                        </div>

                        <div class="flex gap-2 rounded-sm flex-col">
                          <p>{{ item.formatted_updated_at }}</p>
                        </div>
                      </div>
                      <div class="flex flex-col gap-1 ml-16">
                        <div
                          class="rounded-md text-white text-center px-1"
                          :class="{
                            'bg-green-500': submission.status == 'graduated',
                            'bg-yellow-500': submission.status != 'graduated',
                          }"
                        >
                          {{ submission.status }}
                        </div>

                        <Link
                          :href="`/participant/participant/certificate/${submission.certificate?.credential_id}`"
                          v-if="submission.status == 'graduated'"
                          class="rounded-md text-white text-center px-1 bg-orange-500 hover:bg-orange-600"
                        >
                          Sertifikat
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- <div
                  v-else
                  class="flex gap-2 items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900"
                  v-for="(item, index) in props.histories"
                  :key="index"
                >
                  <div
                    v-for="(submission, indexSubmission) in item.submissions"
                    :key="indexSubmission"
                  >
                    {{ submission.schedule.class_room.name }}
                    {{ submission.schedule.category.name }}
                    {{ submission.schedule.regency_regional.regency }}
                    {{ submission.status }}

                    <div
                      class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 w-full cursor-pointer"
                    >
                      <img
                      class="object-cover w-full rounded-t-lg h-96 md:h-auto md:w-48 md:rounded-none md:rounded-s-lg"
                      :src="item.poster"
                      alt=""
                    />
                      <div
                        class="flex flex-col justify-between p-4 leading-normal"
                      >
                        <h5
                          class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                          {{ submission.schedule.class_room.name }} |
                          {{ submission.schedule.category.name }}
                          <small class="text-xs text-yellow-500 absolute"
                            >Lulus</small
                          >
                        </h5>
                        <div class="flex gap-2 bg-gray-400 p-1 rounded-sm">
                          <p>{{ item.start_date_class }}</p>
                          <p>-</p>
                          <p>{{ item.end_date_class }}</p>
                        </div>
                        <p
                          class="mb-3 font-normal text-gray-700 dark:text-gray-400"
                        >
                          {{ item.facility }}
                        </p>
                        <p
                          class="mb-3 font-normal text-gray-700 dark:text-gray-400"
                        >
                          {{ item.benefit }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div> -->
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main modal -->
      <div
        id="submission-modal"
        tabindex="-1"
        aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full"
      >
        <div class="relative p-4 w-full max-w-2xl max-h-full">
          <!-- Modal content -->
          <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div
              @click="closeModal('submission-modal')"
              class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600"
            >
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                Upload Bukti Pembayaran
              </h3>
              <button
                type="button"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                data-modal-hide="submission-modal"
              >
                <svg
                  class="w-3 h-3"
                  aria-hidden="true"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 14 14"
                >
                  <path
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                  />
                </svg>
                <span class="sr-only">Close modal</span>
              </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5 space-y-4">
              <p class="text-center">{{ nameClassRoom }} | {{ category }}</p>

              <input
                type="file"
                ref="fileInput"
                accept="image/*"
                @change="handleFileUpload"
                class="hidden"
              />

              <div @click="triggerFileInput" class="cursor-pointer">
                <div v-if="previewProof == null" class="w-5/12 mx-auto">
                  <svg
                    fill="#000000"
                    viewBox="0 0 1920 1920"
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
                        d="M915.744 213v702.744H213v87.842h702.744v702.744h87.842v-702.744h702.744v-87.842h-702.744V213z"
                        fill-rule="evenodd"
                      ></path>
                    </g>
                  </svg>
                </div>
                <div v-else class="w-full">
                  <img :src="previewProof" width="100%" />
                </div>
              </div>
            </div>
            <!-- Modal footer -->
            <div
              class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600"
            >
              <button
                @click="hanldeRegisterClass"
                data-modal-hide="submission-modal"
                type="button"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
              >
                Kirim
              </button>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayoutParticipant>
  </div>
</template>
