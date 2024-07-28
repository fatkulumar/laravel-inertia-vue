<script setup>
import AuthenticatedLayoutCommittee from "@/Layouts/AuthenticatedLayoutCommittee.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { computed, onMounted, ref, watch } from "vue";
// import Pagination from "@/Components/Partials/Pagination.vue";
import Swal from "sweetalert2";
// import { Modal } from "flowbite";
import TabMenu from "@/Components/Committee/TabMenu.vue";

onMounted(() => {
  initFlowbite();
});

const props = defineProps({
  documentations: {
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
  id: "",
  schedule_id: idSubmissionLastSegment,
  title: "",
  description: "",
  image: "",
});

function resetForm() {
  form.id = "";
  form.title = "";
  form.description = "";
  form.image = "";
  previewImage.value = "";
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

const closeModal = (targetModal = "crud-modal") => {
  resetForm();
  formCheckbox.id = [];
  formCheckbox.status = "";
  const $targetEl = document.getElementById(targetModal);
  const modal = new Modal($targetEl);
  modal.hide();
};

const showModal = (targetModal = "crud-modal") => {
  const $targetEl = document.getElementById(targetModal);
  const modal = new Modal($targetEl);
  modal.show();
};

const formCheckbox = useForm({
  id: [],
  status: "",
});

const previewImage = ref();
function uploadImage(e) {
  const image = e.target.files[0];
  if (
    (image.type == "image/png") |
    (image.type == "image/jpg") |
    (image.type == "image/jpeg")
  ) {
    const reader = new FileReader();
    reader.readAsDataURL(image);
    reader.onload = (e) => {
      previewImage.value = e.target.result;
      form.image = image;
    };
  } else {
    form.image = null;
    closeModal("crud-modal");
    toast("warning", "Harus Format Gambar");
  }
}

function addDocumentation() {
  form.post("/committee/schedule/documentation/store", {
    preserveScroll: true,
    onSuccess: () => {
      closeModal("crud-modal");
      toast("success", "Berhasil");
    },
  });
}

function deleteDocumentation(id, title) {
  form.id = id
  const konfirm = confirm(`Apakah anda yakin ingin menghapus? ${title}`);
  if (!konfirm) return;
  form.delete(`/committee/schedule/documentation/delete`, {
    preserveScroll: true,
    onSuccess: () => {
      resetForm();
      toast("success", "Data Berhasil Dihapus");
    },
  });
}

function editDocumentation(data) {
  previewImage.value = data.image;
  form.id = data.id;
  form.title = data.title;
  form.description = data.description;
  form.image = data.image;
  showModal("crud-modal");
}
</script>

<template>
  <Head title="Documentation" />
  <div>
    <AuthenticatedLayoutCommittee>
      <template #header>
        <TabMenu :id="idSubmissionLastSegment" />
      </template>
      <template #headerTitle>
        Dokumentasi
      </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex">
            <div class="p-6 text-gray-900">
              <div
                class="text-red-600 text-sm ml-2"
                v-for="(error, index) in props.errors"
                :key="index"
              >
                *{{ error }}
              </div>

              <div class="my-2">
                <!-- icon plus -->
                <div
                  @click="showModal()"
                  title="Tambah Dokumentasi"
                  class="cursor-pointer"
                >
                  <svg
                    class="h-8 w-8 bg-green-400 p-1 rounded-lg"
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
                        d="M4 12H20M12 4V20"
                        stroke="#000000"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      ></path>
                    </g>
                  </svg>
                </div>
              </div>

              <div class="max-w-screen-xl mx-auto p-5 sm:p-10 md:p-1">
                <div
                  class="grid grid-cols-1 md:grid-cols-3 sm:grid-cols-2 gap-10"
                >
                  <div
                    v-for="(item, index) in props.documentations"
                    :key="index"
                    class="border-r border-b border-l border-gray-400 lg:border-t lg:border-gray-400 bg-white rounded-b lg:rounded-b-none lg:rounded-r flex flex-col justify-between leading-normal"
                  >
                    <img
                      :src="item.image"
                      class="w-full mb-3"
                    />
                    <div class="p-4 pt-2">
                      <div class="mb-8">
                        <div
                          class="text-gray-900 font-bold text-lg mb-2 hover:text-indigo-600 inline-block"
                          >{{ item.title }}</div>
                        <p class="text-gray-700 text-sm">
                         {{ item.description }}
                        </p>
                      </div>
                      <div class="grid grid-cols-2 gap-2">
                        <button @click="editDocumentation(item)" title="Edit Dokumentasi" class="bg-blue-500 p-2 rounded-lg text-white" type="button">Edit</button>
                        <button @click="deleteDocumentation(item.id, item.title)" title="Hapus Dokumentasi" class="bg-red-500 p-2 rounded-lg text-white" type="button">Hapus</button>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main modal -->
      <div
        id="crud-modal"
        tabindex="-1"
        aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full"
      >
        <div class="relative p-4 w-full max-w-7xl max-h-full">
          <!-- Modal content -->
          <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div
              class="text-red-600 text-sm ml-2"
              v-for="(error, index) in props.errors"
              :key="index"
            >
              *{{ error }}
            </div>
            <div
              class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600"
            >
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Tambah Dokumentasi
              </h3>
              <button
                type="button"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                @click="closeModal()"
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
            <form
              @submit.prevent="addDocumentation"
              enctype="multipart/form-data"
              class="p-4 md:p-5"
            >
              <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                  <div class="grid gap-4 mb-4 grid-cols-2">
                    <div class="col-span-2">
                      <label
                        for="image"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >image</label
                      >
                      <img :src="previewImage" class="md:w-5/12 py-2" />
                      <input
                        @change="uploadImage"
                        type="file"
                        name="image"
                        id="image"
                        accept="image/*"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Judul Dokumentasi"
                      />
                    </div>
                  </div>

                  <label
                    for="title"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Title</label
                  >
                  <input
                    v-model="form.title"
                    type="text"
                    name="title"
                    id="title"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Judul Dokumentasi"
                  />
                </div>
              </div>

              <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                  <label
                    for="description"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >description</label
                  >
                  <textarea
                    v-model="form.description"
                    type="text"
                    name="description"
                    id="description"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Judul Dokumentasi"
                  />
                </div>
              </div>
              <button
                title="Tambah Dokumentasi"
                type="submit"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
              >
                {{ form.id ? "Update Dokumentasi" : "Add Dokumentasi" }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </AuthenticatedLayoutCommittee>
  </div>
</template>
