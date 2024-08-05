<script setup>
import AuthenticatedLayoutAdmin from "@/Layouts/AuthenticatedLayoutAdmin.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Pagination from "@/Components/Partials/Pagination.vue";
import Swal from "sweetalert2";
import { Modal } from "flowbite";

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

let search = ref(props.filters.search);

watch(search, (value) => {
  router.get(
    "/dashboard/schedule",
    { search: value },
    {
      preserveState: true,
      replace: true,
    }
  );
});

const form = useForm({
  id: "",
  participant_id: "",
  committee_id: "",
  status: "",
  approval_date: "",
  graduation_date: "",
  file: "",
});

function resetForm() {
  form.id = "";
  form.participant_id = "";
  form.committee_id = "";
  form.status = "";
  form.approval_date = "";
  form.graduation_date = "";
  form.file = "";
}

function modalRoom(opt) {
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

// function addsubmission() {
//   form.post("/dashboard/schedule/store", {
//     preserveScroll: true,
//     onSuccess: () => {
//         resetForm();
//         modalRoom("hide");
//         toast("success", "Data Berhasil Ditambah");
//     }
//     });
// }

function editClassRoom(data) {
  form.id = data.id;
  form.participant_id = data.participant_id;
  form.committee_id = data.committee_id;
  form.status = data.status;
  form.approval_date = data.approval_date;
  form.graduation_date = data.graduation_date;
  form.file = data.file;
  modalRoom("show");
}

function rejectSchedule(id, namePeserta) {
  form.id = id;
  const konfirm = confirm(`Apakah anda yakin ingin menolak ${namePeserta}?`);
  if (!konfirm) return;
  form.post(`/dashboard/schedule/reject-schedule`, {
    preserveScroll: true,
    onSuccess: (e) => {
      toast("success", "Berhasil");
      resetForm();
    },
  });
}

function overviewSchedule(id, namePeserta) {
  form.id = id;
  const konfirm = confirm(`Apakah anda yakin ingin overview ${namePeserta}?`);
  if (!konfirm) return;
  form.post(`/dashboard/schedule/overview-schedule`, {
    preserveScroll: true,
    onSuccess: (e) => {
      toast("success", "Berhasil");
      resetForm();
    },
  });
}

function receivedSchedule(id, namePeserta) {
  form.id = id;
  const konfirm = confirm(`Apakah anda yakin ingin received ${namePeserta}?`);
  if (!konfirm) return;
  form.post(`/dashboard/schedule/received-schedule`, {
    preserveScroll: true,
    onSuccess: (e) => {
      toast("success", "Berhasil");
      resetForm();
    },
  });
}

function approvalSchedule(id, namePeserta) {
  form.id = id;
  const konfirm = confirm(`Apakah anda yakin ingin menerima ${namePeserta}?`);
  if (!konfirm) return;
  form.post(`/dashboard/schedule/approval-schedule`, {
    preserveScroll: true,
    onSuccess: (e) => {
      toast("success", "Berhasil");
      resetForm();
    },
  });
}

function deleteSchedule(id, nameClass, category) {
  const konfirm = confirm(`Hapus ${nameClass} ${category}?`);
  if (!konfirm) return;
  form.id = id;
  form.delete(`/dashboard/schedule/delete-schedule/${id}`, {
    preserveScroll: true,
    onSuccess: (e) => {
      toast("success", "Berhasil");
      resetForm();
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

function handleOptionSchedule() {
  formCheckbox.post("/dashboard/schedule/option-schedule", {
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
//   formCheckbox.post("/dashboard/schedule/destroy", {
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
</script>

<template>
  <Head title="Article" />
  <div>
    <AuthenticatedLayoutAdmin>
      <!-- <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Article</h2>
            </template> -->
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <div
                  class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900"
                >
                  <!-- <div>
                    icon plus
                    <div
                      @click="showModal()"
                      title="Tambah Artikel"
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
                  </div> -->

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
                      placeholder="Search for Schedule"
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
                        <p>Kelas</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Kategori</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Regional</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Email Peserta</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Panitia</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Proposal</p>
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
                        <p>Tanggal Overview</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Tanggal Diterima</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Tanggal Disetujui</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Tanggal Kelulusan</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Status</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <div class="flex gap-1 items-center">
                          <p class="text-center mt-1">Action</p>
                          <input
                            @click="checkedAll()"
                            class="h-6 w-6"
                            type="checkbox"
                            :id="`checkboxAll`"
                          />

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
                        {{ item.class_room?.name }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.category?.name }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.regional?.name }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.committee?.email }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.committee?.name }}
                      </td>
                      <td class="px-6 py-4">
                        <a
                          class="text-blue-500 underline"
                          :href="item.link_proposal"
                          target="_blank"
                          rel="noopener noreferrer"
                          >{{ item.proposal }}</a
                        >
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
                        {{ item.date_overview ? item.date_overview : "-----" }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.date_received ? item.date_received : "-----" }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.approval_date ? item.approval_date : "-----" }}
                      </td>
                      <td class="px-6 py-4">
                        {{
                          item.graduation_date ? item.graduation_date : "-----"
                        }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.status }}
                      </td>
                      <!-- <td class="px-6 py-4">
                        <div class="flex gap-2">
                          <div
                            @click="approvalSchedule(item)"
                            title="Diterima"
                            class="bg-green-100 p-0.5 rounded-md"
                          >
                            <svg height="25x" width="25px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 46.372 46.372" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <path style="fill:#010002;" d="M45.668,9.281l-4.914-4.914c-0.905-0.905-2.409-0.944-3.36-0.089L18.665,21.124 c-0.951,0.855-2.504,0.868-3.469,0.028l-5.09-4.433c-0.965-0.84-2.48-0.788-3.385,0.117l-6.042,6.042 c-0.905,0.905-0.905,2.371,0,3.276L15.82,41.295c0,0,0.491,0.491,1.096,1.096c0.605,0.605,1.79,0.325,2.645-0.626l26.194-29.123 C46.612,11.69,46.572,10.186,45.668,9.281z"></path> </g> </g></svg>
                          </div>

                          <div
                            @click="rejectSchedule(item.id, item.participant?.name)"
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
                            @click="deleteSchedule(item.id, item.class_room?.name, item.category?.name)"
                            title="Hapus"
                            class="bg-purple-100 p-0.5 rounded-md cursor-pointer"
                          >
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <path fill="none" d="M0 0h24v24H0z"/>
                            <path d="M3 6h18v2H3V6zm3 3h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V9zm2-7h8v2H8V2zm2 2h4v2h-4V4z"/>
                          </svg>

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
                              <li>
                                <button
                                  title="Overview"
                                  @click="overviewSchedule(item.id, item.participant?.name)"
                                  type="button"
                                  class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                  Overview
                                </button>
                              </li>
                              <li>
                                <button
                                  title="Received"
                                  @click="receivedSchedule(item.id, item.participant?.name)"
                                  type="button"
                                  class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                  Received
                                </button>
                              </li>
                              <li>
                                <button
                                  title="Reject"
                                  @click="rejectSchedule(item.id, item.participant?.name)"
                                  type="button"
                                  class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                  Reject
                                </button>
                              </li>
                              <li>
                                <button
                                  title="Approval"
                                  @click="approvalSchedule(item)"
                                  type="button"
                                  class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                  Approval
                                </button>
                              </li>
                              <li>
                                <button
                                  title="Hapus"
                                  @click="
                                    deleteSchedule(
                                      item.id,
                                      item.class_room?.name,
                                      item.category?.name
                                    )
                                  "
                                  type="button"
                                  class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                  Hapus
                                </button>
                              </li>
                              <li>
                                <button
                                  title="Hapus"
                                  type="button"
                                  class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                  Laporan
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
              @submit.prevent="handleOptionSchedule"
              enctype="multipart/form-data"
              class="p-4 md:p-5"
            >
              <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                  <label
                    for="graduation"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Graduation</label
                  >
                  <select
                    id="graduation"
                    name="graduation"
                    v-model="formCheckbox.status"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  >
                    <option selected value="">Choose a Option</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="pending">Pending</option>
                  </select>
                </div>
              </div>
              <button
                title="Tambah Kelas"
                type="submit"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
              >
                {{ form.id ? "Update" : "Update" }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </AuthenticatedLayoutAdmin>
  </div>
</template>
