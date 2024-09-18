<script setup>
import AuthenticatedLayoutAdmin from "@/Layouts/AuthenticatedLayoutAdmin.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { onMounted, ref, watch } from "vue";
import Pagination from "@/Components/Partials/Pagination.vue";
import Swal from "sweetalert2";
import { initFlowbite, Modal } from "flowbite";
import axios from "axios";

onMounted(() => {
    initFlowbite();
});

const props = defineProps({
  submissions: {
    type: Object,
    default: () => ({}),
  },
  committees: {
    type: Object,
    default: () => ({}),
  },
  participants: {
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

const form = useForm({
  id: "",
  participant_id: "",
  committee_id: "",
  status: "",
//   approval_date: "",
//   graduation_date: "",
//   file: "",
  submission_id: "",
  credential_id: "",
  proof: "",
  schedule_id: "",
});

function resetForm() {
  form.id = "";
  form.participant_id = "";
  form.committee_id = "";
  form.status = "";
//   form.approval_date = "";
//   form.graduation_date = "";
//   form.file = "";
  form.submission_id = "";
  form.credential_id = "";
  form.proof = null;
  form.schedule_id = null;
}

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

function addsubmission() {
  form.post("/dashboard/submission/store", {
    preserveScroll: true,
    onSuccess: () => {
      resetForm();
      closeModal("crud-submission");
      toast("success", "Data Berhasil Ditambah");
    },
  });
}

async function EditSubmission(data) {
  form.id = data.id;
  form.participant_id = data.participant_id;
  form.committee_id = data.schedule?.committee_id;
  form.status = data.status;
  form.proof = data.proof;
  form.schedule_id = data.schedule?.id;
  previewProof.value = data.link_proof;
  try {
    await chainedSchedule(data.schedule?.committee_id);
  } catch (error) {
    console.error('Error in chainedSchedule:', error);
  }

  showModal("crud-submission");
}

function rejectSubmission(id, namePeserta) {
  form.id = id;
  const konfirm = confirm(`Apakah anda yakin ingin menolak ${namePeserta}?`);
  if (!konfirm) return;
  form.post(`/dashboard/submission/reject-submission`, {
    preserveScroll: true,
    onSuccess: (e) => {
      toast("success", "Berhasil");
      resetForm();
    },
  });
}

function approvalSubmission(id, namePeserta) {
  form.id = id;
  const konfirm = confirm(`Apakah anda yakin ingin menerima ${namePeserta}?`);
  if (!konfirm) return;
  form.post(`/dashboard/submission/approval-submission`, {
    preserveScroll: true,
    onSuccess: (e) => {
      toast("success", "Berhasil");
      resetForm();
    },
  });
}

function deleteSubmission(id, nameClass, category) {
  form.id = id;
  const konfirm = confirm(`Hapus ${nameClass} ${category}?`);
  if (!konfirm) return;
  form.delete(`/dashboard/submission/delete-submission/${id}`, {
    preserveScroll: true,
    onSuccess: (e) => {
      toast("success", "Berhasil");
      resetForm();
    },
  });
}

function graduatedSubmission(id, namePeserta) {
  form.id = id;
  const konfirm = confirm(`Apakah lulus ${namePeserta}?`);
  if (!konfirm) return;
  form.post(`/dashboard/submission/graduated-submission`, {
    preserveScroll: true,
    onSuccess: (e) => {
      toast("success", "Berhasil");
      resetForm();
    },
  });
}

function certificateSubmission(
  submissionId,
  participantId,
  credentialId = null
) {
  form.participant_id = participantId;
  form.submission_id = submissionId;
  form.credential_id = credentialId;
  showModal("certificate-modal");
}

function handleCertificateSubmission() {
  form.post(`/dashboard/submission/certificate-submission`, {
    preserveScroll: true,
    onSuccess: (e) => {
      toast("success", "Berhasil");
      closeModal("certificate-modal");
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
    const submissId = formCheckbox.id.includes(id);
    if (!submissId) {
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
  if (props.submissions.to == totalChecked) {
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
    props.submissions.data.forEach((data) => {
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

function optionSubmission(targetModal = "crud-modal") {
  showModal(targetModal);
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

const previewProof = ref(null);
function uploadProof(e) {
  const proof = e.target.files[0];
  if (
    proof.type == "image/png" ||
    proof.type == "image/jpg" ||
    proof.type == "image/jpeg"
  ) {
    const reader = new FileReader();
    reader.readAsDataURL(proof);
    reader.onload = (e) => {
      previewProof.value = e.target.result;
      form.proof = proof;
    };
  } else {
    form.proof = null;
    toast("warning", "Harus Format Gambar");
  }
}

const schedule = ref([]);
const chainedSchedule = async (scheduleId) => {
    if(scheduleId) {
        await axios
        .get(`/dashboard/submission/schedule/${scheduleId}`)
        .then((response) => {
        schedule.value = response.data;
        })
        .catch((error) => console.error(error));
    }else{
        schedule.value = []
    }
};

</script>

<template>
  <Head title="Pengajuan Kelas" />
  <div>
    <AuthenticatedLayoutAdmin>
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Pengajuan Kelas
        </h2>
      </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div
            class="p-6 flex flex-col items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900"
          >
            <div>
              <!-- icon plus -->
              <div
                @click="showModal('crud-submission')"
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
                placeholder="Nama"
              />
            </div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <!-- <div
                class="text-red-600 text-sm ml-2"
                v-for="(error, index) in props.errors"
                :key="index"
              >
                *{{ error }}
              </div> -->
              <div class="overflow-x-auto shadow-md sm:rounded-lg">
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
                        <p>Nama</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Email Peserta</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Kelas</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Regional</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Bukti Pembayaran</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Status</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Tanggal Mulai Kelas</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Tanggal Selesai Kelas</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Sertifikat</p>
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
                            title="Pilih Status"
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
                      v-for="(item, index) in props.submissions.data"
                      :key="index"
                      class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                      <td class="px-6 py-4">
                        {{ props.submissions.from + index }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.participant?.name }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.participant?.email }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.schedule?.class_room?.name }}
                        {{ item.schedule?.category?.name }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.schedule?.regional?.name }}
                      </td>
                      <td class="px-6 py-4">
                        <img
                          width="100%"
                          :src="item.link_proof"
                          alt=""
                          srcset=""
                        />
                      </td>
                      <td class="px-6 py-4">
                        {{ item.status }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.formatted_start_date_class }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.formatted_end_date_class }}
                      </td>
                      <td
                        class="px-6 py-4"
                        v-if="item.certificate?.credential_id"
                      >
                        <a
                          class="text-blue-500 hover:underline"
                          :href="`/certificate/${item.certificate?.credential_id}`"
                          target="_blank"
                          >Sertifikat {{ item.participant?.name }}</a
                        >
                      </td>
                      <td class="px-6 py-4" v-else>Sertifikat Belum Ada</td>
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
                                  title="Terima"
                                  @click="
                                    approvalSubmission(
                                      item.id,
                                      item.participant?.name
                                    )
                                  "
                                  type="button"
                                  class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                  Terima
                                </button>
                              </li>
                              <li>
                                <button
                                  title="Tolak"
                                  @click="
                                    rejectSubmission(
                                      item.id,
                                      item.participant?.name
                                    )
                                  "
                                  type="button"
                                  class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                  Tolak
                                </button>
                              </li>
                              <li>
                                <button
                                  title="Hapus"
                                  @click="
                                    deleteSubmission(
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
                                  title="Edit"
                                  @click="
                                    EditSubmission(item)
                                  "
                                  type="button"
                                  class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                  Edit
                                </button>
                              </li>
                              <li>
                                <button
                                  title="Lulus"
                                  @click="
                                    graduatedSubmission(
                                      item.id,
                                      item.participant?.name
                                    )
                                  "
                                  type="button"
                                  class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                  Lulus
                                </button>
                              </li>
                              <li v-if="item.status == 'graduated'">
                                <button
                                  title="Sertifikat"
                                  @click="
                                    certificateSubmission(
                                      item.id,
                                      item.participant?.id,
                                      item.certificate?.credential_id
                                    )
                                  "
                                  type="button"
                                  class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                  Lihat Sertifikat
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
              </div>
              <Pagination
                class="my-6 flex justify-center md:justify-end"
                :links="props.submissions.links"
              />
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
                Status
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
              @submit.prevent="handleOptionSubmission"
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
                    <option selected value="">Pilih Status</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="pending">Pending</option>
                    <option value="graduated">Graduated</option>
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

      <!-- certificate modal -->
      <div
        id="certificate-modal"
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
                Sertifikat
              </h3>
              <button
                type="button"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                @click="closeModal('certificate-modal')"
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
              @submit.prevent="handleCertificateSubmission"
              class="p-4 md:p-5"
            >
              <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                  <label
                    for="credential_id"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Nomor Sertifikat</label
                  >
                  <input
                    v-model="form.credential_id"
                    :readonly="
                      form.credential_id &&
                      props.submissions.data[0]?.certificate?.credential_id
                    "
                    type="text"
                    name="credential_id"
                    id="credential_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Nomor Kredensial Sertifikat"
                  />
                </div>
                <div
                  class="col-span-2 text-red-600 text-xs"
                  v-if="
                    form.credential_id &&
                    props.submissions.data[0]?.certificate?.credential_id
                  "
                >
                  Sudah ada sertifikat
                </div>
              </div>
              <button
                title="Tambah Kelas"
                :disabled="
                  form.credential_id &&
                  props.submissions.data[0]?.certificate?.credential_id
                "
                type="submit"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
              >
                {{
                  form.credential_id &&
                  props.submissions.data[0]?.certificate?.credential_id
                    ? "Update"
                    : "Add"
                }}
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- modal tabah submission -->
      <div
        id="crud-submission"
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
                Tambah Submission
              </h3>
              <button
                type="button"
                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                @click="closeModal('crud-submission')"
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
              @submit.prevent="addsubmission"
              enctype="multipart/form-data"
              class="p-4 md:p-5"
            >
              <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                  <label
                    for="proof"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Bukti Pembayaran</label
                  >
                  <img :src="previewProof" class="w-5/12 py-2" />
                  <input
                    @change="uploadProof"
                    type="file"
                    name="proof"
                    id="proof"
                    accept="image/*"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                  />
                </div>

                <div class="col-span-2 sm:col-span-1">
                  <label
                    for="committee_id"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Panitia</label
                  >
                  <select
                    @change="chainedSchedule(form.committee_id)"
                    v-model="form.committee_id"
                    id="committee_id"
                    name="committee_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  >
                    <option value="" selected>Pilih Panitia</option>
                    <option
                      v-for="(item, index) in props.committees"
                      :key="index"
                      :value="item.id"
                    >
                      {{ item.name }}
                    </option>
                  </select>
                </div>

                <div class="col-span-2 sm:col-span-1">
                  <label
                    for="schedule_id"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Jadwal</label
                  >
                  <select
                    v-model="form.schedule_id"
                    id="schedule_id"
                    name="schedule_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  >
                    <option value="" :selected="form.schedule_id == null">Pilih Jadwal</option>
                    <option
                      v-for="(item, index) in schedule"
                      :key="index"
                      :value="item.id"
                      :selected="item.id == form.schedule_id"
                    >
                      {{ item.class_room?.name }} {{ item.category?.name }}
                    </option>
                  </select>
                </div>

                <div class="col-span-2 sm:col-span-1">
                  <label
                    for="participant_id"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Siswa</label
                  >
                  <select
                    v-model="form.participant_id"
                    id="participant_id"
                    name="participant_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  >
                    <option value="" selected>Pilih Siswa</option>
                    <option
                      v-for="(item, index) in props.participants"
                      :key="index"
                      :value="item.id"
                    >
                      {{ item.name }}
                    </option>
                  </select>
                </div>

                <div class="col-span-2 sm:col-span-1">
                  <label
                    for="status"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Status</label
                  >
                  <select
                    v-model="form.status"
                    id="status"
                    name="status"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  >
                    <option selected value="">Pilih Status</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="pending">Pending</option>
                    <option value="graduated">Graduated</option>
                  </select>
                </div>
              </div>
              <button
                type="submit"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
              >
                {{ form.id ? "Update" : "Add" }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </AuthenticatedLayoutAdmin>
  </div>
</template>
