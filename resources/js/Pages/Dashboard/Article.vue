<script setup>
import AuthenticatedLayoutAdmin from "@/Layouts/AuthenticatedLayoutAdmin.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Pagination from "@/Components/Partials/Pagination.vue";
import Swal from "sweetalert2";
import { Modal } from "flowbite";
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

const editor = ref(ClassicEditor);
const editorData = ref('<p>Content of the editor.</p>');
const editorConfig = ref({
    // The configuration of the editor.
    extraPlugins: [ MyCustomUploadAdapterPlugin ],
});

class MyUploadAdapter {
    constructor( loader ) {
        // The file loader instance to use during the upload.
        this.loader = loader;
    }

    // Starts the upload process.
    upload() {
        return this.loader.file
            .then( file => new Promise( ( resolve, reject ) => {
                this._initRequest();
                this._initListeners( resolve, reject, file );
                this._sendRequest( file );
            } ) );
    }

    // Aborts the upload process.
    abort() {
        if ( this.xhr ) {
            this.xhr.abort();
        }
    }

    // Initializes the XMLHttpRequest object using the URL passed to the constructor.
    _initRequest() {
        const xhr = this.xhr = new XMLHttpRequest();

        // Note that your request may look different. It is up to you and your editor
        // integration to choose the right communication channel. This example uses
        // a POST request with JSON as a data structure but your configuration
        // could be different.
        xhr.open( 'POST', '/dashboard/article/upload', true );
         // Set CSRF token header.
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        xhr.responseType = 'json';
    }

    // Initializes XMLHttpRequest listeners.
    _initListeners( resolve, reject, file ) {
        const xhr = this.xhr;
        const loader = this.loader;
        const genericErrorText = `Couldn't upload file: ${ file.name }.`;

        xhr.addEventListener( 'error', () => reject( genericErrorText ) );
        xhr.addEventListener( 'abort', () => reject() );
        xhr.addEventListener( 'load', () => {
            const response = xhr.response;

            // This example assumes the XHR server's "response" object will come with
            // an "error" which has its own "message" that can be passed to reject()
            // in the upload promise.
            //
            // Your integration may handle upload errors in a different way so make sure
            // it is done properly. The reject() function must be called when the upload fails.
            if ( !response || response.error ) {
                return reject( response && response.error ? response.error.message : genericErrorText );
            }

            // If the upload is successful, resolve the upload promise with an object containing
            // at least the "default" URL, pointing to the image on the server.
            // This URL will be used to display the image in the content. Learn more in the
            // UploadAdapter#upload documentation.
            resolve( {
                default: response.url
            } );
        } );

        // Upload progress when it is supported. The file loader has the #uploadTotal and #uploaded
        // properties which are used e.g. to display the upload progress bar in the editor
        // user interface.
        if ( xhr.upload ) {
            xhr.upload.addEventListener( 'progress', evt => {
                if ( evt.lengthComputable ) {
                    loader.uploadTotal = evt.total;
                    loader.uploaded = evt.loaded;
                }
            } );
        }
    }

    // Prepares the data and sends the request.
    _sendRequest( file ) {
        // Prepare the form data.
        const data = new FormData();

        data.append( 'upload', file );

        // Important note: This is the right place to implement security mechanisms
        // like authentication and CSRF protection. For instance, you can use
        // XMLHttpRequest.setRequestHeader() to set the request headers containing
        // the CSRF token generated earlier by your application.

        // Send the request.
        this.xhr.send( data );
    }
}

function MyCustomUploadAdapterPlugin( editor ) {
    editor.plugins.get( 'FileRepository' ).createUploadAdapter = ( loader ) => {
        // Configure the URL to the upload script in your back-end here!
        return new MyUploadAdapter( loader );
    };
}

const props = defineProps({
  articles: {
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
});

let search = ref(props.filters.search);

watch(search, (value) => {
  router.get(
    "/dashboard/article",
    { search: value },
    {
      preserveState: true,
      replace: true,
    }
  );
});

const previewImage = ref(null);
const form = useForm({
  id: "",
  title: "",
  body: "",
  label: "",
  category_id: "",
  image: null,
});

function resetForm() {
  form.id = "";
  form.title = "";
  form.body = "";
  form.label = "";
  form.category_id = "null";
  image.value = null;
  previewImage.value = null;
}

function modalArticle(opt) {
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

function addArticle() {
  form.post("/dashboard/article/store", {
        preserveScroll: true,
        onSuccess: (e) => {
            toast("success", "Berhasil");
            resetForm();
            closeModal();
        },
    });
}

function editArticle(data) {
  form.id = data.id;
  form.title = data.title;
  form.body = data.body;
  form.label = data.label;
  form.category_id = data.category_id;
  modalArticle("show");
}

function deleteArticle(id, title) {
  const konfirm = confirm(`Apakah anda yakin ingin menghapus ${title}?`);
  if (!konfirm) return;
  form.delete(`/dashboard/article/delete/${id}`, {
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
  if (props.articles.to == totalChecked) {
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
    props.articles.data.forEach((data) => {
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

function deleteArticleChoice() {
  const konfirm = confirm(
    `Apakah anda yakin ingin menghapus data ini?`
  );
  if (!konfirm) return;
  formCheckbox.post("/dashboard/article/destroy", {
    preserveScroll: true,
    onSuccess: () => {
      formCheckbox.id = [];
      toast("success", "Data Berhasil Dihapus");
      let checkedCheckboxes = document.querySelectorAll('input[type="checkbox"]:checked');
        checkedCheckboxes.forEach(element => {
            element.checked = false
        });

      deleteChoice.value = false
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
                      placeholder="Search for users"
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
                        <p class="text-center">Judul</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p class="text-center">Artikel</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p class="text-center">Status</p>
                      </th>
                      <th scope="col" class="px-6 py-3">
                        <p class="text-center">Tanggal</p>
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
                            @click="deleteArticleChoice()"
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
                      v-for="(item, index) in props.articles.data"
                      :key="index"
                      class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                      <th
                        scope="row"
                        class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white"
                      >
                        <img
                          class="w-16 h-16 rounded-full"
                          :src="item.image"
                          alt="Jese image"
                        />
                        <div class="ps-3">
                          <div class="text-base font-semibold">
                            {{ item.title }}
                          </div>
                          <div class="font-normal text-yellow-500">
                            {{ item.category_id ?? item.category_id }}
                          </div>
                          <div class="font-normal text-green-500">
                            {{ item.label ?? item.label }}
                          </div>
                        </div>
                      </th>
                      <td class="px-6 py-4">
                        <p class="line-clamp-2" v-html="item.body"></p>
                      </td>
                      <td class="px-6 py-4">
                        <div class="flex items-center">
                          <div
                            class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"
                          ></div>
                          Publish
                        </div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="flex items-center">
                          <div
                            class="h-2.5 w-2.5 rounded-full bg-green-500 me-2"
                          ></div>
                          {{
                            item.updated_at ? item.updated_at : item.created_at
                          }}
                        </div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="flex gap-2">
                          <div
                            @click="editArticle(item)"
                            title="Edit"
                            class="bg-green-100 p-0.5 rounded-md"
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
                                <path
                                  d="m104.175 90.97-4.252 38.384 38.383-4.252L247.923 15.427V2.497L226.78-18.646h-12.93zm98.164-96.96 31.671 31.67"
                                  class="cls-1"
                                  style="
                                    fill: none;
                                    fill-opacity: 1;
                                    fill-rule: nonzero;
                                    stroke: #000000;
                                    stroke-width: 12;
                                    stroke-linecap: round;
                                    stroke-linejoin: round;
                                    stroke-dasharray: none;
                                    stroke-opacity: 1;
                                  "
                                  transform="translate(-77.923 40.646)"
                                ></path>
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
                            @click="deleteArticle(item.id, item.title)"
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
                                <path
                                  d="M10 12L14 16M14 12L10 16M18 6L17.1991 18.0129C17.129 19.065 17.0939 19.5911 16.8667 19.99C16.6666 20.3412 16.3648 20.6235 16.0011 20.7998C15.588 21 15.0607 21 14.0062 21H9.99377C8.93927 21 8.41202 21 7.99889 20.7998C7.63517 20.6235 7.33339 20.3412 7.13332 19.99C6.90607 19.5911 6.871 19.065 6.80086 18.0129L6 6M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6"
                                  stroke="#000000"
                                  stroke-width="2"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                ></path>
                              </g>
                            </svg>
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
                  :links="props.articles.links"
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
            <div class="text-red-600 text-sm ml-2" v-for="error, index in props.errors" :key="index">
                *{{ error }}
            </div>

            <div
              class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600"
            >
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Tambah Artikel
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
              @submit.prevent="addArticle"
              enctype="multipart/form-data"
              class="p-4 md:p-5"
            >
              <div class="grid gap-4 mb-4 grid-cols-2">
                <div class="col-span-2">
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
                </div>
                <div class="col-span-2 sm:col-span-1">
                  <label
                    for="label"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Label</label
                  >
                  <input
                    v-model="form.label"
                    type="text"
                    name="label"
                    id="label"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Label Artikel"
                  />
                </div>
                <div class="col-span-2 sm:col-span-1">
                  <label
                    for="category_id"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Kategori</label
                  >
                  <select
                    v-model="form.category_id"
                    id="category_id"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                  >
                    <option :selected="form.category_id == null" value="">Select a Category</option>
                    <option v-for="item, index in categories" :key="index" :value="item.id">
                        {{ item.name }}
                    </option>
                  </select>
                </div>
                <div class="col-span-2">
                  <label
                    for="body"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >Isi Artikel</label
                  >
                  <ckeditor v-model="form.body" :editor="editor" :config="editorConfig"></ckeditor>
                  <!-- <textarea
                    v-model="form.body"
                    id="body"
                    rows="4"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    placeholder="Lorem ipsum dolor sit amet consectetur adipisicing elit. Sit, corporis."
                  ></textarea> -->
                </div>
              </div>
              <button
                title="Tambah Artikel"
                type="submit"
                class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
              >
                {{ form.id ? "Update Artikel" : "Add Artikel" }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </AuthenticatedLayoutAdmin>
  </div>
</template>
