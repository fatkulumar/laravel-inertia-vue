/<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Pagination from "@/Components/Partials/Pagination.vue";
import Swal from "sweetalert2";
import { Modal } from "flowbite";

const props = defineProps({
  schedule: {
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

const linkFile = ref(props.schedule[0].linkFile);
const committeeName = ref(props.schedule[0].committee?.name)
const regionalName = ref(props.schedule[0].committee?.profile?.regional?.name)

const form = useForm({
  id: props.schedule[0].id,
  regional_id: props.schedule[0].committee?.profile?.regional?.id,
  committee_id: props.schedule[0].committee?.id,
  hp: props.schedule[0].committee?.profile?.hp,
  start_date_class: props.schedule[0].start_date_class,
  end_date_class: props.schedule[0].end_date_class,
  location: props.schedule[0].location,
  google_maps: props.schedule[0].location,
  address: props.schedule[0].address,
  periode: props.schedule[0].periode,
  file: props.schedule[0].file,
});

function resetForm() {
    form.id = ""
    form.regional_id = ""
    form.committee_id = ""
    form.hp = ""
    form.start_date_class = ""
    form.end_date_class = ""
    form.location = ""
    form.google_maps = ""
    form.address = ""
    form.file = ""
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

function addsubmission() {
  form.post("/dashboard/submission/store", {
    preserveScroll: true,
    onSuccess: () => {
      resetForm();
      modalRoom("hide");
      toast("success", "Data Berhasil Ditambah");
    },
  });
}

function editClassRoom(data) {
  form.id = data.id;
  form.regional_id = data.regional_id;
  form.committee_id = data.committee_id;
  form.status = data.status;
  form.approval_date = data.approval_date;
  form.graduation_date = data.graduation_date;
  form.file = data.file;
  modalRoom("show");
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
      modalUser("hide");
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
      modalUser("hide");
    },
  });
}

function graduationSubmission(id, namePeserta) {
  form.id = id;
  const konfirm = confirm(`Apakah anda yakin ingin meluluskan ${namePeserta}?`);
  if (!konfirm) return;
  form.post(`/dashboard/submission/graduation-submission`, {
    preserveScroll: true,
    onSuccess: (e) => {
      toast("success", "Berhasil");
      resetForm();
      modalUser("hide");
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
    <AuthenticatedLayout>
      <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Jadwal</h2>
      </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <!-- <div
                  class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900"
                >
                  <Link class="bg-green-400 rounded-md py-1 px-2 text-white" href="">Tambah Jadwal</Link>

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
                      placeholder="Search for users"
                    />
                  </div>
                </div> -->

                <!-- disini -->
                <form
                  @submit.prevent="addArticle"
                  enctype="multipart/form-data"
                  class="p-4 md:p-5"
                >
                  <div class="grid gap-4 mb-4 grid-cols-2">
                    <!-- <div class="col-span-2">
                    <label
                      for="image"
                      class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                      >Image</label
                    >
                    <img :src="previewImage" class="w-32" />
                    <input
                      @change="uploadImage"
                      type="file"
                      name="image"
                      id="image"
                      accept="image/*"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    />
                  </div>
                  <div class="col-span-2">
                    <label
                      for="title"
                      class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                      >Judul</label
                    >
                    <input
                      v-model="form.title"
                      type="text"
                      name="title"
                      id="title"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="Judul Artikel"
                    />
                  </div> -->
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
                      />
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                      <label
                        for="periode"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Angkatan Ke</label
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
                      <img :src="linkFile" class="w-32 py-2" />
                      <div class="flex items-center">
                        <div class="w-2/12">
                          <input
                            @change="uploadImage"
                            type="file"
                            name="image"
                            id="image"
                            accept="image/jpg, jpeg, png"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                          />
                        </div>
                        <a :href="linkFile" class="ml-2 text-blue-500 underline">{{ form.file }}</a>
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
                    <option selected value="">Choose a graduation</option>
                    <option value="Diterima">Diterima</option>
                    <option value="Ditolak">Ditolak</option>
                    <option value="Lulus">Lulus</option>
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
    </AuthenticatedLayout>
  </div>
</template>