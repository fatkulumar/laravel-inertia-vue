<script setup>
import AuthenticatedLayoutAdmin from "@/Layouts/AuthenticatedLayoutAdmin.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Pagination from "@/Components/Partials/Pagination.vue";
import Swal from "sweetalert2";
import { Modal } from "flowbite";

const props = defineProps({
  users: {
    type: Object,
    default: () => ({}),
  },
  regionals: {
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
    "/dashboard/user",
    { search: value },
    {
      preserveState: true,
      replace: true,
    }
  );
});

const form = useForm({
  id: "",
  name: "",
  email: "",
  role: "",
  gender: "",
  password: "mmpj12345",
  regional_id: "",
});

function resetForm() {
  form.id = "";
  form.name = "";
  form.email = "";
  form.role = "";
  form.password = "mmpj12345";
  form.regional_id = null;
  form.gender = null;
}

function modalUser(opt) {
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

function addUser() {
form.post("/dashboard/user/store", {
    preserveScroll: true,
    onSuccess: (e) => {
        toast("success", "Berhasil");
        resetForm();
        modalUser("hide");
    },
    });
}

function editUser(data, role, regional) {
  form.id = data.id;
  form.name = data.name;
  form.email = data.email;
  form.role = role[0].name;
  form.regional_id = regional.regional_id;
  form.gender = regional.gender;
  modalUser("show");
}

function deleteUser(id, name) {
  const konfirm = confirm(`Apakah anda yakin ingin menghapus ${name}?`);
  if (!konfirm) return;
  form.delete(`/dashboard/user/delete/${id}`, {
    preserveScroll: true,
    onSuccess: () => {
      resetForm();
      toast("success", "Data Berhasil Dihapus");
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
  resetForm()
  formCheckbox.id = []
  const $targetEl = document.getElementById(targetModal);
  const modal = new Modal($targetEl);
  modal.hide();
};

const showModal = (targetModal = "crud-modal") => {
  resetForm()
  const $targetEl = document.getElementById(targetModal);
  const modal = new Modal($targetEl);
  modal.show();
};

const formCheckbox = useForm({
  id: [],
});

const deleteChoice = ref(false)
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
  if (props.users.to == totalChecked) {
    checkboxAll.checked = true;
  }
  if(formCheckbox.id.length > 0) {
    deleteChoice.value = true
  }else{
    deleteChoice.value = false
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
    props.users.data.forEach((data) => {
      formCheckbox.id.push(data.id);
      countCheckbox.value++;
    });
  } else {
    uncheckedCheckboxes.forEach((checkbox) => {
      checkbox.checked = false;
    });
    formCheckbox.id = [];
  }

  if(formCheckbox.id.length > 0) {
    deleteChoice.value = true
  }else{
    deleteChoice.value = false
  }
}

function deleteUserChoice() {
  const konfirm = confirm(
    `Apakah anda yakin ingin menghapus data ini?`
  );
  if (!konfirm) return;
  formCheckbox.post("/dashboard/user/destroy", {
    preserveScroll: true,
    onSuccess: () => {
      formCheckbox.id = [];
      toast("success", "Data Berhasil Dihapus");
      let checkedCheckboxes = document.querySelectorAll('input[type="checkbox"]:checked');
        checkedCheckboxes.forEach(element => {
            element.checked = false
        });
    },
  });
}

function uploadImage(e) {
    const image = e.target.files[0];
    if (image.type == 'image/png' | image.type == 'image/jpg' | image.type == 'image/jpeg') {
        const reader = new FileReader();
        reader.readAsDataURL(image);
        reader.onload = e => {
            previewImage.value = e.target.result;
            form.image = image;
        };
    } else {
        form.image = null;
        closeModal('crud-modal');
        toast('warning', 'Harus Format Gambar')
    }
}
</script>

<template>
  <Head title="Users" />
  <div>
    <AuthenticatedLayoutAdmin>
      <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users</h2>
            </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
            class="p-6 flex flex-col items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900"
          >
            <div>
              <!-- icon plus -->
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
              <div class="overflow-x-auto shadow-md sm:rounded-lg">

                <table
                  class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                >
                  <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                  >
                    <tr>
                      <th scope="col" class="px-6 py-3">
                        <p>Nama</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Email</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Regional</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Role</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p>Jenis Kelamin</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <div class="flex gap-1">
                          <p class="text-center mt-1">Action</p>
                          <input
                            @click="checkedAll()"
                            class="h-6 w-6"
                            type="checkbox"
                            :id="`checkboxAll`"
                          />

                          <div
                            @click="deleteUserChoice()"
                            v-show="deleteChoice"
                            title="Hapus"
                            class="bg-red-100 p-0.5 rounded-md"
                          >
                            <svg
                              class="h-6 w-6 cursor-pointer"
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
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3 6.38597C3 5.90152 3.34538 5.50879 3.77143 5.50879L6.43567 5.50832C6.96502 5.49306 7.43202 5.11033 7.61214 4.54412C7.61688 4.52923 7.62232 4.51087 7.64185 4.44424L7.75665 4.05256C7.8269 3.81241 7.8881 3.60318 7.97375 3.41617C8.31209 2.67736 8.93808 2.16432 9.66147 2.03297C9.84457 1.99972 10.0385 1.99986 10.2611 2.00002H13.7391C13.9617 1.99986 14.1556 1.99972 14.3387 2.03297C15.0621 2.16432 15.6881 2.67736 16.0264 3.41617C16.1121 3.60318 16.1733 3.81241 16.2435 4.05256L16.3583 4.44424C16.3778 4.51087 16.3833 4.52923 16.388 4.54412C16.5682 5.11033 17.1278 5.49353 17.6571 5.50879H20.2286C20.6546 5.50879 21 5.90152 21 6.38597C21 6.87043 20.6546 7.26316 20.2286 7.26316H3.77143C3.34538 7.26316 3 6.87043 3 6.38597Z" fill="#1C274C"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M11.5956 22.0001H12.4044C15.1871 22.0001 16.5785 22.0001 17.4831 21.1142C18.3878 20.2283 18.4803 18.7751 18.6654 15.8686L18.9321 11.6807C19.0326 10.1037 19.0828 9.31524 18.6289 8.81558C18.1751 8.31592 17.4087 8.31592 15.876 8.31592H8.12404C6.59127 8.31592 5.82488 8.31592 5.37105 8.81558C4.91722 9.31524 4.96744 10.1037 5.06788 11.6807L5.33459 15.8686C5.5197 18.7751 5.61225 20.2283 6.51689 21.1142C7.42153 22.0001 8.81289 22.0001 11.5956 22.0001ZM10.2463 12.1886C10.2051 11.7548 9.83753 11.4382 9.42537 11.4816C9.01321 11.525 8.71251 11.9119 8.75372 12.3457L9.25372 17.6089C9.29494 18.0427 9.66247 18.3593 10.0746 18.3159C10.4868 18.2725 10.7875 17.8856 10.7463 17.4518L10.2463 12.1886ZM14.5746 11.4816C14.9868 11.525 15.2875 11.9119 15.2463 12.3457L14.7463 17.6089C14.7051 18.0427 14.3375 18.3593 13.9254 18.3159C13.5132 18.2725 13.2125 17.8856 13.2537 17.4518L13.7537 12.1886C13.7949 11.7548 14.1625 11.4382 14.5746 11.4816Z" fill="#1C274C"></path> </g></svg>
                              </g>
                            </svg>
                          </div>
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(item, index) in props.users.data"
                      :key="index"
                      class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                      <td class="px-6 py-4">
                        {{ item.name }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.email }}
                      </td>
                      <td class="px-6 py-4">
                        {{ item.profile?.regional?.name }}
                      </td>
                      <td class="px-6 py-4">
                        <div v-for="(itemRole, indexRole) in item.roles" :key="indexRole">
                            {{ itemRole.name }}
                        </div>
                      </td>
                      <td class="px-6 py-4">
                        {{ item.profile?.gender }}
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
                              <li>
                                <button
                                  title="Edit Ketua"
                                    @click="editUser(item, item.roles, item.profile)"
                                  type="button"
                                  class="inline-flex w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                  Edit
                                </button>
                              </li>
                              <li>
                                <button
                                  title="Hapus Ketua"
                                  @click="deleteUser(item.id, item.title)"
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
                    </tr>
                  </tbody>
                </table>

            </div>
            <Pagination
              class="my-6 flex justify-center md:justify-end"
              :links="props.users.links"
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
            <div class="text-red-600 text-sm ml-2" v-for="error, index in props.errors" :key="index">
                *{{ error }}
            </div>

            <div
              class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600"
            >

            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                User
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
              @submit.prevent="addUser"
              enctype="multipart/form-data"
              class="p-4 md:p-5"
            >
              <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
                  <label
                    for="name"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Nama</label
                  >
                  <input
                    v-model="form.name"
                    type="text"
                    name="name"
                    id="name"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Nama"
                  />
                </div>
                <div class="col-span-2">
                    <label
                      for="email"
                      class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                      >Email</label
                    >
                    <input
                      v-model="form.email"
                      type="text"
                      name="email"
                      id="email"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                      placeholder="Email"
                    />
                </div>
                <div class="col-span-2">
                    <label
                      for="role"
                      class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                      >Role</label
                    >
                    {{ form.role }}
                    <select v-model="form.role" name="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option :selected="form.role == null" value="">Choose a Role</option>
                        <option value="peserta">Peserta</option>
                        <!-- <option value="admin">Admin</option> -->
                        <option value="panitia">Panitia</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label
                      for="regional_id"
                      class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                      >Regional</label
                    >
                    <select v-model="form.regional_id" name="regional_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option :selected="form.regional_id == null" value="">Choose a Regional</option>
                        <option v-for="item, index in regionals" :key="index" :value="item.id" :selected="form.regionId === item.id">
                            {{ item.name }}
                        </option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label
                      for="role"
                      class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                      >Gender</label
                    >
                    <select v-model="form.gender" name="role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option :selected="form.gender == null" value="">Pilih Jenis Kelamin</option>
                        <option value="laki-laki">Laki Laki</option>
                        <option value="perempuan">Perempuan</option>
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
