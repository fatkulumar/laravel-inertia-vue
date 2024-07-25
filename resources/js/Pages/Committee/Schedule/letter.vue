<script setup>
import AuthenticatedLayoutCommittee from "@/Layouts/AuthenticatedLayoutCommittee.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import Pagination from "@/Components/Partials/Pagination.vue";
import Swal from "sweetalert2";
import { Modal } from "flowbite";
import axios from "axios";
import TabMenu from "@/Components/Committee/TabMenu.vue";

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

const form = useForm({
  id: props.letters.data[0]?.id,
  schedule_id: props.letters.data[0]?.schedule_id,
  file: "",
  name: "",
})

function resetForm() {
  form.file = "";
  form.name = "";
}

// function modalSpeaker(opt) {
//   const $targetEl = document.getElementById("crud-modal");
//   // options with default values
//   const options = {
//     placement: "bottom-right",
//     backdrop: "dynamic",
//     backdropClasses: "bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40",
//     closable: false,
//   };

//   // instance options object
//   const instanceOptions = {
//     id: "crud-modal",
//     override: true,
//   };

//   const modal = new Modal($targetEl, options, instanceOptions);
//   if (opt == "hide") {
//     modal.hide();
//   }
//   if (opt == "show") {
//     modal.show();
//   }
// }

function addLetter() {
  form.post("/committee/schedule/upload-letter/" + props.letters.data[0].schedule_id, {
    preserveScroll: true,
    onSuccess: (e) => {
      toast("success", "Berhasil");
      resetForm();
      closeModal();
    },
  });
}

// function editSpeaker(data) {
//   form.id = data.id;
//   form.name = data.name;
//   form.image = data.image;
//   form.class_room_id = data.class_room_id;
//   form.category_id = data.category_id;
//   form.city_code = data.city_code;
//   form.province_code = data.province_code;
//   previewImage.value = data.image;
//   modalSpeaker("show");
// }

// function deleteLetter(id, name) {
//   const konfirm = confirm(`Apakah anda yakin ingin menghapus ${name}?`);
//   if (!konfirm) return;
//   form.delete(`/committee/schedule/delete-letter/${id}`, {
//     preserveScroll: true,
//     onSuccess: () => {
//       resetForm();
//       toast("success", "Data Berhasil Dihapus");
//     },
//   });
// }

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

// const closeModal = (targetModal = "crud-modal") => {
//   resetForm();
//   formCheckbox.id = [];
//   const $targetEl = document.getElementById(targetModal);
//   const modal = new Modal($targetEl);
//   modal.hide();
// };

// const showModal = (targetModal = "crud-modal") => {
//   resetForm();
//   const $targetEl = document.getElementById(targetModal);
//   const modal = new Modal($targetEl);
//   modal.show();
// };

// const formCheckbox = useForm({
//   id: [],
// });

// const deleteChoice = ref(false);
// function toggleCheckbox(id) {
//   let checkbox = document.getElementById(`checkbox${id}`);
//   let checkboxAll = document.getElementById(`checkboxAll`);
//   if (checkboxAll.checked) {
//     checkboxAll.checked = false;
//   }

//   if (checkbox.checked == true) {
//     const articleId = formCheckbox.id.includes(id);
//     if (!articleId) {
//       formCheckbox.id.push(id);
//     }
//   } else {
//     formCheckbox.id = formCheckbox.id.filter((checkId) => checkId !== id); // Memfilter id pengguna yang cocok
//   }

//   const checkboxes = document.querySelectorAll('input[type="checkbox"]');

//   // Inisialisasi jumlah total checkbox yang dicentang
//   let totalChecked = 0;

//   // Iterasi melalui setiap elemen checkbox
//   checkboxes.forEach((checkbox) => {
//     // Periksa apakah checkbox dicentang
//     if (checkbox.checked) {
//       // Jika dicentang, tambahkan 1 ke jumlah total
//       totalChecked++;
//     }
//   });
//   if (props.d.to == totalChecked) {
//     checkboxAll.checked = true;
//   }
//   if (formCheckbox.id.length > 0) {
//     deleteChoice.value = true;
//   } else {
//     deleteChoice.value = false;
//   }
// }

// const countCheckbox = ref(0);
// function checkedAll() {
//   countCheckbox.value = 0;
//   let checkedCheckboxes = document.querySelectorAll(
//     'input[type="checkbox"]:not(#checkboxAll):not(:checked)'
//   );
//   let uncheckedCheckboxes = document.querySelectorAll(
//     'input[type="checkbox"]:not(#checkboxAll)'
//   );
//   let checboxAll = document.getElementById("checkboxAll");
//   if (checboxAll.checked == true) {
//     checkedCheckboxes.forEach((checkbox) => {
//       checkbox.checked = true;
//     });
//     props.d.data.forEach((data) => {
//       formCheckbox.id.push(data.id);
//       countCheckbox.value++;
//     });
//   } else {
//     uncheckedCheckboxes.forEach((checkbox) => {
//       checkbox.checked = false;
//     });
//     formCheckbox.id = [];
//   }

//   if (formCheckbox.id.length > 0) {
//     deleteChoice.value = true;
//   } else {
//     deleteChoice.value = false;
//   }
// }

// function deleteSpeakerChoice() {
//   const konfirm = confirm(`Apakah anda yakin ingin menghapus data ini?`);
//   if (!konfirm) return;
//   formCheckbox.post("/dashboard/speaker/destroy", {
//     preserveScroll: true,
//     onSuccess: () => {
//       formCheckbox.id = [];
//       toast("success", "Data Berhasil Dihapus");
//       let checkedCheckboxes = document.querySelectorAll(
//         'input[type="checkbox"]:checked'
//       );
//       checkedCheckboxes.forEach((element) => {
//         element.checked = false;
//       });

//       deleteChoice.value = false;
//     },
//   });
// }

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
            <TabMenu :id="props.letters.data[0]?.schedule_id" />
      </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <div class="bg-gray-100 flex items-center mb-2">
                    <div class="w-1/12 bg-gray-200 flex">
                        <p class="w-1/12 p-6 text-red-500 font-bold text-lg">
                            PDF
                        </p>
                    </div>
                    <div class="ml-2">
                        <a class="hover:underline hover:text-blue-500" :href="props.letters.data[0].link_file" target="_blank" rel="noopener noreferrer">{{ props.letters.data[0]?.name }}</a>
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
