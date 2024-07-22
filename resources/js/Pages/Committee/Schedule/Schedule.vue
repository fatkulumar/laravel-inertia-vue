<script setup>
import AuthenticatedLayoutCommittee from "@/Layouts/AuthenticatedLayoutCommittee.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { computed, onMounted, ref, watch } from "vue";
import Pagination from "@/Components/Partials/Pagination.vue";
import Swal from "sweetalert2";
import { Modal } from "flowbite";

onMounted(() => {
  initFlowbite();
});

const props = defineProps({
  schedules: {
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
  filters: {
    type: Object,
    default: () => ({}),
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
  committee: {
    type: Object,
    default: () => ({}),
  },
  committees: {
    type: Object,
    default: () => ({}),
  },
  typeActivities: {
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

const committeeName = computed(() => props.committee.name);
const regionalName = computed(() => props.committee.profile?.regional?.name);
// const linkFile = ref(props.schedule[0].linkFile);

const form = useForm({
  id: "",
  regional_id: props.committee.profile?.regional?.id,
//   participant_id: "",
  committee_id: props.committee.id,
  hp: props.committee.profile?.hp,
  category_id: "",
  class_room_id: "",
  chief_id: "", //ketua pelaksana
  hp_chief: "", //ketua pelaksana
  type_activity_id: "", //jenis kegiatan
  periode: "",
  poster: "", //konsep kegiatan
  concept: "", //konsep kegiatan
  committee_layout: "", //susunan panitia
  target_participant: "", //target peserta
  speaker: "", //pemateri
  total_activity: "", // total kegiatan yang sudah dikerjakan
  price: "", // harga
  facility: "", // fasiliitas
  total_rooms_stay: "", // jumlah ruang menginap
  benefit: "", // jumlah ruang menginap
  location: "",
  google_maps: "",
  address: "",
  status: "pending",
  start_date_class: "",
  end_date_class: "",
  approval_date: "",
  graduation_date: "",
  proposal: "",
});

function resetForm() {
  form.id = "",
//   form.participant_id = "",
  form.committee_id = "",
  form.category_id = "",
  form.class_room_id = "",
  form.chief_id = "", //ketua pelaksana
  form.type_activity_id = "", //jenis kegiatan
  form.periode = "",
  form.poster = "", //konsep kegiatan
  form.concept = "", //konsep kegiatan
  form.committee_layout = "", //susunan panitia
  form.target_participant = "", //target peserta
  form.speaker = "", //pemateri
  form.total_activity = "", // total kegiatan yang sudah dikerjakan
  form.price = "", // harga
  form.facility = "", // fasiliitas
  form.total_rooms_stay = "", // jumlah ruang menginap
  form.benefit = "", // jumlah ruang menginap
  form.location = "",
  form.google_maps = "",
  form.address = "",
  form.status = "",
  form.start_date_class = "",
  form.end_date_class = "",
  form.approval_date = "",
  form.graduation_date = "",
  form.proposal = "",
  previewPoster.value = "";
  previewProposal.value = "";
}

function modalAddSchedule(opt) {
  const $targetEl = document.getElementById("crud-modal");
  // options with default values
  const options = {
    placement: "bottom-right",
    backdrop: "dynamic",
    backdropClasses: "bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40",
    closable: false,
  };

  // instance options object
  const instanceOptions = {
    id: "crud-modal",
    override: true,
  };

  const modal = new Modal($targetEl, options, instanceOptions);
  if (opt == "hide") {
    modal.hide();
  }
  if (opt == "show") {
    modal.show();
  }
}

function addSchedule() {
  form.post("/committee/schedule/store", {
    preserveScroll: true,
    onSuccess: () => {
      resetForm();
      modalAddSchedule("hide");
      toast("success", "Data Berhasil Ditambah");
    },
  });
}

// function editClassRoom(data) {
//     form.id = data.id;
//     form.participant_id = data.participant_id;
//     form.committee_id = data.committee_id;
//     form.status = data.status;
//     form.approval_date = data.approval_date;
//     form.graduation_date = data.graduation_date;
//     form.file = data.file;
//     modalAddSchedule("show");
// }

// function rejectSubmission(id, namePeserta) {
//     form.id = id;
//     const konfirm = confirm(`Apakah anda yakin ingin menolak ${namePeserta}?`);
//     if (!konfirm) return;
//     form.post(`/dashboard/submission/reject-submission`, {
//         preserveScroll: true,
//         onSuccess: (e) => {
//             toast("success", "Berhasil");
//             resetForm();
//             modalUser("hide");
//         },
//     });
// }

// function approvalSubmission(id, namePeserta) {
//     form.id = id;
//     const konfirm = confirm(`Apakah anda yakin ingin menerima ${namePeserta}?`);
//     if (!konfirm) return;
//     form.post(`/dashboard/submission/approval-submission`, {
//         preserveScroll: true,
//         onSuccess: (e) => {
//             toast("success", "Berhasil");
//             resetForm();
//             modalUser("hide");
//         },
//     });
// }

// function graduationSubmission(id, namePeserta) {
//     form.id = id;
//     const konfirm = confirm(`Apakah anda yakin ingin meluluskan ${namePeserta}?`);
//     if (!konfirm) return;
//     form.post(`/dashboard/submission/graduation-submission`, {
//         preserveScroll: true,
//         onSuccess: (e) => {
//             toast("success", "Berhasil");
//             resetForm();
//             modalUser("hide");
//         },
//     });
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
  if (props.schedules.to == totalChecked) {
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
    props.schedules.data.forEach((data) => {
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

function optionSubmission() {
  showModal();
}

function handleOptionSubmission() {
  formCheckbox.post("/dashboard/submission/option-submission", {
    preserveScroll: true,
    onSuccess: () => {
      choice.value = false;
      formCheckbox.id = [];
      formCheckbox.status = "";
      toast("success", "Berhasil");
      closeModal();
      let checkedCheckboxes = document.querySelectorAll(
        'input[type="checkbox"]:checked'
      );
      checkedCheckboxes.forEach((element) => {
        element.checked = false;
      });
    },
  });
}
// function optionSubmission() {
//   const konfirm = confirm(
//     `Apakah anda yakin ingin menghapus data ini?`
//   );
//   if (!konfirm) return;
//   formCheckbox.post("/dashboard/submission/destroy", {
//     preserveScroll: true,
//     onSuccess: () => {
//       formCheckbox.id = [];
//       toast("success", "Data Berhasil Dihapus");
//       let checkedCheckboxes = document.querySelectorAll('input[type="checkbox"]:checked');
//         checkedCheckboxes.forEach(element => {
//             element.checked = false
//         });
//     },
//   });
// }

const previewPoster = ref(null);
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
    toast("warning", "Harus Format Gambar");
  }
}

const previewProposal = ref(null);
function uploadProposal(e) {
  const image = e.target.files[0];
  if (
    (image.type == "image/png") |
    (image.type == "image/jpg") |
    (image.type == "image/jpeg")
  ) {
    const reader = new FileReader();
    reader.readAsDataURL(image);
    reader.onload = (e) => {
      previewProposal.value = e.target.result;
      form.proposal = image;
    };
  } else {
    form.image = null;
    toast("warning", "Harus Format Gambar");
  }
}

function deleteSchedule(id) {
  const konfirm = confirm(`Apakah anda yakin ingin menghapus?`);
  if (!konfirm) return;
  form.delete(`/committee/schedule/delete/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      resetForm();
      toast("success", "Data Berhasil Dihapus");
    },
  });
}
</script>

<template>
  <Head title="Article" />
  <div>
    <AuthenticatedLayoutCommittee>
      <template #header> Jadwal </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <div
                  class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900"
                >
                  <div>
                    <!-- icon plus -->
                    <div
                      @click="showModal()"
                      title="Tambah Jadwal"
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

                  <label for="table-search" class="sr-only">Search</label>
                  <div class="relative">
                    <div
                      class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none"
                    >
                      <svg
                        class="w-4 h-4 text-gray-500 dark:text-gray-400"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 20 20"
                      >
                        <path
                          stroke="currentColor"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"
                        />
                      </svg>
                    </div>
                    <input
                      v-model="search"
                      type="text"
                      id="table-search-users"
                      class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                      placeholder="Regional"
                    />
                  </div>
                </div>
                <table
                  class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                >
                  <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                  >
                    <tr>
                      <th scope="col" class="px-6 py-3">
                        <p>No</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Regional</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Tanggal Mulai Kelas</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Tanggal Selesai Kelas</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Tanggal Pengajuan</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Status</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Tanggal Disetujui</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <div class="flex gap-1 items-center">
                          <p class="text-center mt-1">Action</p>
                          <div
                            @click="optionSubmission()"
                            v-show="choice"
                            title="Pilihan"
                            class="bg-red-100 p-0.5 rounded-md cursor-pointer"
                          >
                            <svg
                              class="h-8 w-8"
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
                                <g id="Edit / Select_Multiple">
                                  <path
                                    id="Vector"
                                    d="M3 9V19.4C3 19.9601 3 20.2399 3.10899 20.4538C3.20487 20.642 3.35774 20.7952 3.5459 20.8911C3.7596 21 4.0395 21 4.59846 21H15.0001M17 8L13 12L11 10M7 13.8002V6.2002C7 5.08009 7 4.51962 7.21799 4.0918C7.40973 3.71547 7.71547 3.40973 8.0918 3.21799C8.51962 3 9.08009 3 10.2002 3H17.8002C18.9203 3 19.4801 3 19.9079 3.21799C20.2842 3.40973 20.5905 3.71547 20.7822 4.0918C21.0002 4.51962 21.0002 5.07969 21.0002 6.19978L21.0002 13.7998C21.0002 14.9199 21.0002 15.48 20.7822 15.9078C20.5905 16.2841 20.2842 16.5905 19.9079 16.7822C19.4805 17 18.9215 17 17.8036 17H10.1969C9.07899 17 8.5192 17 8.0918 16.7822C7.71547 16.5905 7.40973 16.2842 7.21799 15.9079C7 15.4801 7 14.9203 7 13.8002Z"
                                    stroke="#000000"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                  ></path>
                                </g>
                              </g>
                            </svg>
                          </div>
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(item, index) in props.schedules.data"
                      :key="index"
                      class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                      <td class="px-6 py-4">
                        {{ props.schedules.from + index }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.committee?.profile?.regional?.name }}
                      </td>
                      <td class="px-6 py-4">
                        {{
                          item.start_date_class
                            ? item.start_date_class
                            : "-----"
                        }}
                      </td>
                      <td class="px-6 py-4">
                        {{
                          item.end_date_class ? item.end_date_class : "-----"
                        }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.created_at }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.status }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.approval_date ? item.approval_date : "-----" }}
                      </td>

                      <td class="px-6 py-4">
                        <div class="flex gap-2">
                          <div
                            title="Actions"
                            class="w-5 cursor-pointer"
                            id="dropdown-button"
                            :data-dropdown-toggle="`dropdown${index}`"
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
                            :id="`dropdown${index}`"
                            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700"
                          >
                            <ul
                              class="py-2 text-sm text-gray-700 dark:text-gray-200"
                              aria-labelledby="dropdown-button"
                            >
                              <li
                                class="p-2 pl-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                              >
                                <Link
                                 :href="route('committee.schedule.detail', item.id)"
                                  title="Detail Jadwal"
                                  class="cursor-pointer"
                                  >Detail</Link
                                >
                              </li>
                              <li>
                                <button
                                title="Hapus Jadwal"
                                 @click="deleteSchedule(item.id)"
                                  type="button"
                                  class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                  Hapus
                                </button>
                              </li>
                            </ul>
                          </div>
                          <div>
                            <input
                              @click="toggleCheckbox(item.id)"
                              class="h-6 w-6"
                              type="checkbox"
                              :id="`checkbox${item.id}`"
                            />
                          </div>
                        </div>
                      </td>
                      <!-- <td class="px-6 py-4">
                        <div class="flex gap-2">
                          <div
                            @click="approvalSubmission(item)"
                            title="Diterima"
                            class="bg-green-100 p-0.5 rounded-md"
                          >
                            <svg height="25x" width="25px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 46.372 46.372" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <path style="fill:#010002;" d="M45.668,9.281l-4.914-4.914c-0.905-0.905-2.409-0.944-3.36-0.089L18.665,21.124 c-0.951,0.855-2.504,0.868-3.469,0.028l-5.09-4.433c-0.965-0.84-2.48-0.788-3.385,0.117l-6.042,6.042 c-0.905,0.905-0.905,2.371,0,3.276L15.82,41.295c0,0,0.491,0.491,1.096,1.096c0.605,0.605,1.79,0.325,2.645-0.626l26.194-29.123 C46.612,11.69,46.572,10.186,45.668,9.281z"></path> </g> </g></svg>
                          </div>

                          <div
                            @click="rejectSubmission(item.id, item.participant?.name)"
                            title="Diterima"
                            class="bg-red-100 p-0.5 rounded-md cursor-pointer flex items-center"
                          >
                          <svg
                            class="h-6 w-6 cursor-pointer"
                            viewBox="0 0 192 192"
                            xmlns="http://www.w3.org/2000/svg"
                            xml:space="preserve"
                            fill="none"
                            >
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g
                                id="SVGRepo_tracerCarrier"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            ></g>
                            <g id="SVGRepo_iconCarrier">
                                <svg viewBox="0 0 25 25" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <title>Tolak</title> <desc>Created with Sketch Beta.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage"> <g id="Icon-Set-Filled" sketch:type="MSLayerGroup" transform="translate(-469.000000, -1041.000000)" fill="#000000"> <path d="M487.148,1053.48 L492.813,1047.82 C494.376,1046.26 494.376,1043.72 492.813,1042.16 C491.248,1040.59 488.712,1040.59 487.148,1042.16 L481.484,1047.82 L475.82,1042.16 C474.257,1040.59 471.721,1040.59 470.156,1042.16 C468.593,1043.72 468.593,1046.26 470.156,1047.82 L475.82,1053.48 L470.156,1059.15 C468.593,1060.71 468.593,1063.25 470.156,1064.81 C471.721,1066.38 474.257,1066.38 475.82,1064.81 L481.484,1059.15 L487.148,1064.81 C488.712,1066.38 491.248,1066.38 492.813,1064.81 C494.376,1063.25 494.376,1060.71 492.813,1059.15 L487.148,1053.48" id="cross" sketch:type="MSShapeGroup"> </path> </g> </g> </g></svg>
                                <path
                                d="m195.656 33.271-52.882 52.882"
                                style="
                                    fill: none;
                                    fill-opacity: 1;
                                    fill-rule: nonzero;
                                    stroke: #000000;
                                    stroke-width: 12;
                                    stroke-linecap: round;
                                    stroke-linejoin: round;
                                    stroke-miterlimit: 5;
                                    stroke-dasharray: none;
                                    stroke-opacity: 1;
                                "
                                transform="translate(-77.923 40.646)"
                                ></path>
                            </g>
                            </svg>
                          </div>
                          <div
                            @click="graduationSubmission(item)"
                            title="Lulus"
                            class="bg-purple-100 p-0.5 rounded-md cursor-pointer"
                          >
                          <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M11.5842 3.09053C11.8483 2.96982 12.1517 2.96982 12.4158 3.09053L22.4158 7.66195C22.7717 7.82467 23 8.18006 23 8.57143C23 8.96279 22.7717 9.31819 22.4158 9.4809L20 10.5853V16.4469C20 16.648 19.9495 16.8682 19.8221 17.0728C19.4914 17.6042 17.1726 21 12 21C6.82744 21 4.50856 17.6042 4.17785 17.0728C4.0505 16.8682 4 16.648 4 16.4469V10.5853L1.58424 9.4809C1.2283 9.31819 1 8.96279 1 8.57143C1 8.18006 1.2283 7.82467 1.58424 7.66195L11.5842 3.09053ZM6 11.4995V16.1994C6.48987 16.8789 8.31354 19 12 19C15.6865 19 17.5101 16.8789 18 16.1994V11.4995L12.4158 14.0523C12.1517 14.173 11.8483 14.173 11.5842 14.0523L6 11.4995ZM4.40524 8.57143L5.41576 9.03338L12 12.0433L18.5842 9.03338L19.5948 8.57143L12 5.09954L4.40524 8.57143ZM23.5 11.5C23.5 12.3284 22.8284 13 22 13C21.1716 13 20.5 12.3284 20.5 11.5C20.5 10.6716 21.1716 10 22 10C22.8284 10 23.5 10.6716 23.5 11.5ZM23 14.5C23 13.9477 22.5523 13.5 22 13.5C21.4477 13.5 21 13.9477 21 14.5V20C21 20.5523 21.4477 21 22 21C22.5523 21 23 20.5523 23 20V14.5Z" fill="#000000"></path> </g></svg>
                          </div>
                          <div class="flex items-center">
                            <input
                              @click="toggleCheckbox(item.id)"
                              class="h-6 w-6"
                              type="checkbox"
                              :id="`checkbox${item.id}`"
                            />
                          </div>
                        </div>
                      </td> -->
                    </tr>
                  </tbody>
                </table>

                <Pagination
                  class="my-6 flex justify-center md:justify-end"
                  :links="props.schedules.links"
                />
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
                Pilih
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
              @submit.prevent="addSchedule"
              enctype="multipart/form-data"
              class="p-4 md:p-5"
            >
              <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                  <label
                    for="poster"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Poster</label
                  >
                  <img :src="previewPoster" class="w-5/12 py-2" />
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
                    <option v-for="item, index in committees" :key="index" :value="item.id">
                        {{item.name}}
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
                    placeholder="Konsep Kegiatan"
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
                    for="speaker"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Pemateri</label
                  >
                  <input
                    v-model="form.speaker"
                    type="text"
                    name="speaker"
                    id="speaker"
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
                    v-model="form.facility"
                    type="number"
                    name="benefit"
                    id="benefit"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Fasilitas Yang Diberikan Ke Peserta"
                  ></textarea>
                </div>
                <div class="col-span-2 sm:col-span-1">
                <label
                    for="benefit"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Benefit Peserta</label
                >
                <textarea
                    v-model="form.benefit"
                    type="number"
                    name="benefit"
                    id="benefit"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Benefit Peserta"
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
                <div class="col-span-2">
                  <label
                    for="image"
                    class="w-2block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Surat Pengajuan</label
                  >
                  <img :src="previewProposal" class="w-5/12 py-2" />
                  <div class="flex items-center">
                    <div class="w-2/12">
                      <input
                        @change="uploadProposal"
                        type="file"
                        name="image"
                        id="image"
                        accept="image/jpg, jpeg, png"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      />
                    </div>
                    <!-- <a :href="linkFile" class="ml-2 text-blue-500 underline">{{
                      form.file
                    }}</a> -->
                  </div>
                </div>
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
      </div>
    </AuthenticatedLayoutCommittee>
  </div>
</template>
