<script setup>
import AuthenticatedLayoutCommittee from "@/Layouts/AuthenticatedLayoutCommittee.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import Pagination from "@/Components/Partials/Pagination.vue";
import Swal from "sweetalert2";
import { Modal } from "flowbite";
import axios from "axios";
import TabMenuDetailSchedule from "@/Components/Committee/TabMenuDetailSchedule.vue";

onMounted(() => {
  initFlowbite();
});

const props = defineProps({
  letters: {
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
  id: props.letters.data[0]?.id,
  schedule_id: idSubmissionLastSegment,
  file: "",
  name: "",
})

function resetForm() {
  form.file = "";
  form.name = "";
}

function addLetter() {
  form.post("/committee/schedule/upload-letter/" + idSubmissionLastSegment, {
    preserveScroll: true,
    onSuccess: (e) => {
      toast("success", "Berhasil");
      resetForm();
      closeModal();
    },
  });
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

// const previewImage = ref(null);
function uploadLetter(e) {
  const pdf = e.target.files[0];
  if (
    (pdf.type == "application/pdf")
  ) {
    const reader = new FileReader();
    reader.readAsDataURL(pdf);
    reader.onload = (e) => {
      form.file = pdf;
      form.name = pdf.name;
    };
  } else {
    form.pdf = null;
    toast("warning", "Harus Format PDF");
  }
}

// const cities = ref([]);
// const chainedProvince = async (provinceCode) => {
//     await axios
//     .get(`/dashboard/speaker/city/${provinceCode}`)
//     .then((response) => {
//         cities.value = response.data;
//     })
//     .catch((error) => console.error(error));
// };
</script>

<template>
  <Head title="Narasumber" />
  <div>
    <AuthenticatedLayoutCommittee>
      <template #header>
            <TabMenuDetailSchedule :id="idSubmissionLastSegment" />
      </template>
      <template #headerTitle>
        Surat Tugas
      </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <div class="bg-gray-100 flex items-center mb-2" v-if="props.letters.data">
                    <div class="w-1/12 bg-gray-200 flex">
                        <p class="w-1/12 p-6 text-red-500 font-bold text-lg">
                            PDF
                        </p>
                    </div>
                    <div class="ml-2">
                        <a class="hover:underline hover:text-blue-500" :href="props.letters.data[0]?.link_file" target="_blank" rel="noopener noreferrer">{{ props.letters.data[0]?.name }}</a>
                    </div>
                </div>
                <div class="bg-gray-100 flex items-center">
                    <form @submit.prevent="addLetter" class="flex flex-col w-full gap-2">
                        <input @change="uploadLetter" accept="application/pdf" type="file">
                        <button class="w-full p-2 bg-blue-500 text-white rounded-lg" type="submit">Upload</button>
                    </form>
                </div>
              </div>
            </div>
            </div>
        </div>
      </div>
    </AuthenticatedLayoutCommittee>
  </div>
</template>
