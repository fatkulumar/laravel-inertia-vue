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
  schedule: {
    type: Object,
    default: () => ({}),
  },
  classRooms: {
    type: Object,
    default: () => ({}),
  },
  categories: {
    type: Object,
    default: () => ({}),
  },
  chiefs: {
    type: Object,
    default: () => ({}),
  },
  typeActivities: {
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

let search = ref(props.filters.search);

watch(search, (value) => {
  router.get(
    "/dashboard/submission",
    { search: value },
    {
      preserveState: true,
      replace: true,
    }
  );
});

const committeeName = ref(props.schedule[0].committee?.name);
const regionalName = ref(props.schedule[0].committee?.profile?.regional?.name);

const form = useForm({
  id: props.schedule[0].id,
  regional_id: props.schedule[0].committee?.profile?.regional?.id,
  committee_id: props.schedule[0].committee?.id,
  category_id: props.schedule[0].category_id,
  class_room_id: props.schedule[0].class_room_id,
  hp: props.schedule[0].committee?.profile?.hp,
  start_date_class: props.schedule[0].formatted_start_date_class,
  end_date_class: props.schedule[0].formatted_end_date_class,
  location: props.schedule[0].location,
  google_maps: props.schedule[0].location,
  address: props.schedule[0].address,
  periode: props.schedule[0].periode,
  proposal: props.schedule[0].proposal,
  poster: props.schedule[0].poster,
  status: "pending",

  chief_id: props.schedule[0].chief?.id,
  hp_chief: props.schedule[0].chief?.profile?.hp,
  type_activity_id: props.schedule[0].type_activity_id,
  concept: props.schedule[0].concept,
  committee_layout: props.schedule[0].committee_layout,
  target_participant: props.schedule[0].target_participant,
  speaker_id: props.schedule[0].speaker.name,
  total_activity: props.schedule[0].total_activity,
  price: props.schedule[0].price,
  facility: props.schedule[0].facility,
  total_rooms_stay: props.schedule[0].total_rooms_stay,
  benefit: props.schedule[0].benefit,
  created_at: props.schedule[0].formatted_created_at,
  date_overview: props.schedule[0].formatted_date_overview,
  date_received: props.schedule[0].formatted_date_received,
  approval: props.schedule[0].formatted_approval,
});

// function resetForm() {
//     form.id = ""
//     form.regional_id = ""
//     form.committee_id = ""
//     form.category_id = ""
//     form.class_room_id = ""
//     form.hp = ""
//     form.start_date_class = ""
//     form.end_date_class = ""
//     form.location = ""
//     form.google_maps = ""
//     form.address = ""
//     form.file = ""
// }

// function modalRoom(opt) {
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
// //   resetForm();
//   formCheckbox.id = [];
//   formCheckbox.status = "";
//   const $targetEl = document.getElementById(targetModal);
//   const modal = new Modal($targetEl);
//   modal.hide();
// };

// const showModal = (targetModal = "crud-modal") => {
//   const $targetEl = document.getElementById(targetModal);
//   const modal = new Modal($targetEl);
//   modal.show();
// };

const formCheckbox = useForm({
  id: [],
  status: "",
});

const choice = ref(false);
function toggleCheckbox(id) {
  let checkbox = document.getElementById(`checkbox${id}`);
  let checkboxAll = document.getElementById(`checkboxAll`);
  if (checkboxAll.checked) {
    checkboxAll.checked = false;
  }

  if (checkbox.checked == true) {
    const articleId = formCheckbox.id.includes(id);
    if (!articleId) {
      formCheckbox.id.push(id);
    }
  } else {
    formCheckbox.id = formCheckbox.id.filter((checkId) => checkId !== id); // Memfilter id pengguna yang cocok
  }

  const checkboxes = document.querySelectorAll('input[type="checkbox"]');

  // Inisialisasi jumlah total checkbox yang dicentang
  let totalChecked = 0;

  // Iterasi melalui setiap elemen checkbox
  checkboxes.forEach((checkbox) => {
    // Periksa apakah checkbox dicentang
    if (checkbox.checked) {
      // Jika dicentang, tambahkan 1 ke jumlah total
      totalChecked++;
    }
  });
  if (props.schedule.to == totalChecked) {
    checkboxAll.checked = true;
  }
  if (formCheckbox.id.length > 0) {
    choice.value = true;
  } else {
    choice.value = false;
  }
}

const countCheckbox = ref(0);
function checkedAll() {
  countCheckbox.value = 0;
  let checkedCheckboxes = document.querySelectorAll(
    'input[type="checkbox"]:not(#checkboxAll):not(:checked)'
  );
  let uncheckedCheckboxes = document.querySelectorAll(
    'input[type="checkbox"]:not(#checkboxAll)'
  );
  let checboxAll = document.getElementById("checkboxAll");
  if (checboxAll.checked == true) {
    checkedCheckboxes.forEach((checkbox) => {
      checkbox.checked = true;
    });
    props.schedule.data.forEach((data) => {
      formCheckbox.id.push(data.id);
      countCheckbox.value++;
    });
  } else {
    uncheckedCheckboxes.forEach((checkbox) => {
      checkbox.checked = false;
    });
    formCheckbox.id = [];
    formCheckbox.status = "";
  }

  if (formCheckbox.id.length > 0) {
    choice.value = true;
  } else {
    choice.value = false;
  }
}

// function handleOptionSubmission() {
//   formCheckbox.post("/dashboard/submission/option-submission", {
//     preserveScroll: true,
//     onSuccess: () => {
//       choice.value = false;
//       formCheckbox.id = [];
//       formCheckbox.status = "";
//       toast("success", "Berhasil");
//       closeModal();
//       let checkedCheckboxes = document.querySelectorAll(
//         'input[type="checkbox"]:checked'
//       );
//       checkedCheckboxes.forEach((element) => {
//         element.checked = false;
//       });
//     },
//   });
// }

const previewPoster = ref(props.schedule[0].poster);
function uploadPoster(e) {
  const image = e.target.files[0];
  if (
    (image.type == "image/png") |
    (image.type == "image/jpg") |
    (image.type == "image/jpeg")
  ) {
    const reader = new FileReader();
    reader.readAsDataURL(image);
    reader.onload = (e) => {
      previewPoster.value = e.target.result;
      form.poster = image;
    };
  } else {
    form.image = null;
    closeModal("crud-modal");
    toast("warning", "Harus Format Gambar");
  }
}

// const previewProposal = ref(props.schedule[0].proposal);
// function uploadProposal(e) {
//   const image = e.target.files[0];
//   if (
//     (image.type == "image/png") |
//     (image.type == "image/jpg") |
//     (image.type == "image/jpeg")
//   ) {
//     const reader = new FileReader();
//     reader.readAsDataURL(image);
//     reader.onload = (e) => {
//       previewProposal.value = e.target.result;
//       form.proposal = image;
//     };
//   } else {
//     form.image = null;
//     closeModal("crud-modal");
//     toast("warning", "Harus Format Gambar");
//   }
// }

function updateSchedule() {
  form.post("/committee/schedule/store", {
    preserveScroll: true,
    onSuccess: () => {
      toast("success", "Data Berhasil Diedit");
    },
  });
}
</script>

<template>
  <Head title="Article" />
  <div>
    <AuthenticatedLayoutCommittee>
      <template #header>
        <TabMenu :id="props.schedule[0].id" />
      </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div
            class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col md:flex-row"
          >
            <div class="p-6 text-gray-900">
              <div
                class="text-red-600 text-sm ml-2"
                v-for="(error, index) in props.errors"
                :key="index"
              >
                *{{ error }}
              </div>
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <!-- disini -->
                <form
                  @submit.prevent="updateSchedule"
                  enctype="multipart/form-data"
                  class="p-4 md:p-5"
                >
                  <div class="md:grid gap-4 mb-4 md:grid-cols-3">
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="committee_id"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Pengusul</label
                      >
                      <input
                        :value="committeeName"
                        type="text"
                        name="committee_id"
                        id="committee_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Pengusul"
                        readonly
                      />
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="hp"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >HP</label
                      >
                      <input
                        v-model="form.hp"
                        type="text"
                        name="hp"
                        id="hp"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="HP"
                        readonly
                      />
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="regional_id"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Regional</label
                      >
                      <input
                        :value="regionalName"
                        type="text"
                        name="regional_id"
                        id="regional_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Regional"
                        readonly
                      />
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="class_room_id"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Kelas</label
                      >
                      <select
                        v-model="form.class_room_id"
                        id="class_room_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                      >
                        <option selected>Pilih Kelas</option>
                        <option
                          v-for="(item, index) in props.classRooms"
                          :key="index"
                          :value="item.id"
                        >
                          {{ item.name }}
                        </option>
                      </select>
                    </div>

                    <div class="col-span-2">
                      <label
                        for="poster"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Poster</label
                      >
                      <img :src="previewPoster" class="md:w-5/12 py-2" />
                      <input
                        @change="uploadPoster"
                        type="file"
                        name="poster"
                        id="poster"
                        accept="poster/*"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      />
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="chief_id"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Ketua Pelaksana</label
                      >
                      <select
                        v-model="form.chief_id"
                        id="chief_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                      >
                        <option value="" selected>Pilih Ketua Pelaksana</option>
                        <option
                          v-for="(item, index) in chiefs"
                          :key="index"
                          :value="item.id"
                          :selected="item.id == form.chief_id"
                        >
                          {{ item.name }}
                        </option>
                      </select>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="hp"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >HP Ketua Pelaksaa</label
                      >
                      <input
                        v-model="form.hp_chief"
                        type="text"
                        name="hp"
                        id="hp"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="HP"
                      />
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="class_room_id"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Kelas</label
                      >
                      <select
                        v-model="form.class_room_id"
                        id="class_room_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                      >
                        <option value="" selected>Pilih Kelas</option>
                        <option
                          v-for="(item, index) in props.classRooms"
                          :key="index"
                          :value="item.id"
                        >
                          {{ item.name }}
                        </option>
                      </select>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="class_room_id"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Jenis Kegiatan</label
                      >
                      <select
                        v-model="form.type_activity_id"
                        id="class_room_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                      >
                        <option value="" selected>Pilih Jenis Kegiatan</option>
                        <option
                          v-for="(item, index) in props.typeActivities"
                          :key="index"
                          :value="item.id"
                          :selected="item.id == form.type_activity_id"
                        >
                          {{ item.name }}
                        </option>
                      </select>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="category_id"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Tingkatan Kelas</label
                      >
                      <select
                        v-model="form.category_id"
                        id="category_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                      >
                        <option value="" selected>Pilih Tingkatan</option>
                        <option
                          v-for="(item, index) in props.categories"
                          :key="index"
                          :value="item.id"
                        >
                          {{ item.name }}
                        </option>
                      </select>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="periode"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Periode Ke</label
                      >
                      <input
                        v-model="form.periode"
                        type="text"
                        name="periode"
                        id="periode"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Periode Ke"
                      />
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="concept"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Konsep Kegiatan</label
                      >
                      <textarea
                        v-model="form.concept"
                        type="text"
                        name="concept"
                        id="concept"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Konsep Ke"
                      ></textarea>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="committee_layout"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Susunan Panitia</label
                      >
                      <textarea
                        v-model="form.committee_layout"
                        type="text"
                        name="committee_layout"
                        id="committee_layout"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Susunan Panitia"
                      ></textarea>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="target_participant"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Target Peserta</label
                      >
                      <textarea
                        v-model="form.target_participant"
                        type="text"
                        name="target_participant"
                        id="target_participant"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Target Peserta"
                      ></textarea>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="speaker_id"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Pemateri</label
                      >
                      <input
                        v-model="form.speaker_id"
                        type="text"
                        name="speaker_id"
                        id="speaker_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Pemateri"
                      />
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="total_activity"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Total Kegiatan Yang Sudah Dilaksanakan</label
                      >
                      <input
                        v-model="form.total_activity"
                        type="number"
                        name="total_activity"
                        id="total_activity"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Total Kegiatan Yang Sudah Dilaksanakan"
                      />
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="price"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Harga Tiket Masuk</label
                      >
                      <input
                        v-model="form.price"
                        type="number"
                        name="price"
                        id="price"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Harga Tiket Masuk"
                      />
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="total_rooms_stay"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Total Ruangan</label
                      >
                      <input
                        v-model="form.total_rooms_stay"
                        type="number"
                        name="total_rooms_stay"
                        id="total_rooms_stay"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Total Ruangan"
                      />
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="benefit"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Fasilitas Yang Diberikan Ke Peserta</label
                      >
                      <textarea
                        v-model="form.benefit"
                        type="number"
                        name="benefit"
                        id="benefit"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Fasilitas Yang Diberikan Ke Peserta"
                      ></textarea>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="start_date_class"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Mulai</label
                      >
                      <input
                        v-model="form.start_date_class"
                        type="date"
                        name="start_date_class"
                        id="start_date_class"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Tanggal Muali"
                      />
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="end_date_class"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Selesai</label
                      >
                      <input
                        v-model="form.end_date_class"
                        type="date"
                        name="end_date_class"
                        id="end_date_class"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Tanggal Selesai"
                      />
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="location"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Lokasi</label
                      >
                      <input
                        v-model="form.location"
                        type="text"
                        name="location"
                        id="location"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Lokasi"
                      />
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="google_maps"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Google Maps</label
                      >
                      <input
                        v-model="form.google_maps"
                        type="text"
                        name="google_maps"
                        id="google_maps"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Google Maps"
                      />
                    </div>

                    <div class="col-span-2">
                      <label
                        for="alamat"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Alamat</label
                      >
                      <textarea
                        v-model="form.address"
                        id="alamat"
                        name="alamat"
                        rows="4"
                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Alamat"
                      ></textarea>
                    </div>
                    <!-- <div class="col-span-2">
                      <label
                        for="image"
                        class="w-2block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Surat Pengajuan</label
                      >
                      <img :src="previewProposal" class="md:w-5/12 py-2" />
                      <div class="flex items-center">
                        <div>
                          <input
                            @change="uploadProposal"
                            type="file"
                            name="image"
                            id="image"
                            accept="application/pdf"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                          />
                        </div>
                      </div>
                    </div> -->
                  </div>
                  <button
                    title="Update Jadwal"
                    type="submit"
                    class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                  >
                    {{ form.id ? "Update Jadwal" : "Add Jadwal" }}
                  </button>
                </form>
              </div>
            </div>
            <div class="px-10 md:px-0">
              <div class="flex border-b-2 pb-4">
                <p class="mt-6">Status</p>
                <small class="text-xs text-green-500 mt-5"
                  ><span
                    v-if="
                      form.created_at &&
                      form.date_overview &&
                      form.date_received &&
                      form.approval
                    "
                    class="bg-green-500 text-white rounded-2xl p-1 font-bold"
                    >Selesai</span
                  >
                  <span
                    v-else
                    class="bg-red-500 text-white rounded-2xl p-1 font-bold"
                    >Belum Selesai</span
                  ></small
                >
              </div>
              <div class="my-2 flex flex-col gap-2">
                <div class="bg-gray-200 p-2 rounded-lg">
                  <p>Pengajuan</p>
                  <p>{{ form.created_at ? form.created_at : "-" }}</p>
                </div>

                <div class="bg-yellow-100 p-2 rounded-lg">
                  <p>Overview</p>
                  <p>{{ form.date_overview ? form.date_overview : "-" }}</p>
                </div>

                <div class="bg-blue-200 p-2 rounded-lg">
                  <p>Diterima</p>
                  <p>{{ form.date_received ? form.date_received : "-" }}</p>
                </div>

                <div class="bg-green-200 p-2 rounded-lg">
                  <p>Selesai</p>
                  <p>{{ form.approval ? form.approval : "-" }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayoutCommittee>
  </div>
</template>
