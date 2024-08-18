<script setup>
import AuthenticatedLayoutCommittee from "@/Layouts/AuthenticatedLayoutCommittee.vue";
import { Head, useForm } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import TabMenuDetailParticipant from "@/Components/Committee/TabMenuDetailParticipant.vue";

const props = defineProps({
  participant: {
    type: Object,
    default: () => ({}),
  },
  regionals: {
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
  participant_id: props.participant[0]?.id,
  committee_id: "",
  category_id: "",
  id: "",
  name: props.participant[0]?.name,
  email: props.participant[0]?.email,
  address: props.participant[0]?.profile?.address,
  hp: props.participant[0]?.profile?.hp,
  role: "",
  regional: "",
  regional_id: props.participant[0]?.profile?.regional?.id,
  class_room_id: "",
  periode: "",
  location: "",
  google_maps: "",
  status: "pending",
  start_date_class: "",
  end_date_class: "",
  file: "",
  poster: "",
  class_room: "",
  category: "",
});

function updateParticipant() {
  form.post("/committee/participant/update", {
    preserveScroll: true,
    onSuccess: (e) => {
      toast("success", "Berhasil");
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
  <Head title="Detail Peserta" />
  <div>
    <AuthenticatedLayoutCommittee>
      <template #headerTitle> Data Peserta </template>
      <template #header>
        <!-- <h1 class="font-bold">Detail Peserta</h1> -->
        <TabMenuDetailParticipant :id="idSubmissionLastSegment" />
      </template>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <div class="lg:col-span-2 flex md:flex-row flex-col gap-7">
                  <form @submit.prevent="updateParticipant">
                    <div
                      class="grid gap-4 gap-y-2 text-sm grid-cols-1 md:grid-cols-5"
                    >
                      <div class="md:col-span-2">
                        <label for="name">Nama</label>
                        <input
                          v-model="form.name"
                          type="text"
                          name="name"
                          id="name"
                          class="h-10 border mt-1 rounded px-4 w-full bg-gray-50"
                          value=""
                        />
                      </div>

                      <div class="md:col-span-2">
                        <label for="email">Email</label>
                        <input
                          v-model="form.email"
                          type="text"
                          name="email"
                          id="email"
                          class="h-10 border mt-1 rounded px-4 w-full bg-gray-50"
                          value=""
                          placeholder="email@domain.com"
                        />
                      </div>

                      <div class="md:col-span-1">
                        <label for="hp">HP</label>
                        <input
                          v-model="form.hp"
                          type="text"
                          name="hp"
                          id="hp"
                          class="h-10 border mt-1 rounded px-4 w-full bg-gray-50"
                          value=""
                          placeholder=""
                        />
                      </div>

                      <div class="md:col-span-5">
                        <label for="address">Alamat</label>
                        <textarea
                          v-model="form.address"
                          type="text"
                          name="address"
                          id="address"
                          class="h-16 border mt-1 rounded px-4 w-full bg-gray-50"
                          value=""
                          placeholder=""
                        />
                      </div>

                      <div class="md:col-span-3">
                        <label for="regional_id">Regional</label>
                        <select
                          v-model="form.regional_id"
                          type="text"
                          name="regional_id"
                          id="regional_id"
                          class="h-10 border mt-1 rounded px-4 w-full bg-gray-50"
                          value=""
                          placeholder=""
                        >
                          <option value="">
                            Pilih Regional {{ form.regional_id }}
                          </option>
                          <option
                            v-for="(item, index) in regionals"
                            :key="index"
                            :value="item.id"
                            :selected="item.id == form.regional_id"
                          >
                            {{ item.name }}
                          </option>
                        </select>
                      </div>
                      <div class="md:col-span-3 pb-2">
                        <button
                          type="submit"
                          class="bg-blue-500 p-2 rounded-md text-white"
                        >
                          Update
                        </button>
                      </div>
                    </div>
                  </form>
                  <div>
                    <div
                      class="max-w-sm w-full lg:max-w-72 lg:flex"
                      v-for="(item, index) in props.participant[0]?.submissions"
                      :key="index"
                    >
                      <div
                        class="border-r border-b border-l border-gray-400 lg:border-l-1 lg:border-t lg:border-gray-400 bg-white rounded-b lg:rounded-b-none lg:rounded-r p-4 flex flex-col justify-between leading-normal"
                      >
                        <div class="mb-1">
                          <p class="text-sm text-gray-600 flex items-center">
                            {{ item.schedule?.category?.name }}
                          </p>
                          <div class="text-gray-900 font-bold text-xl mb-2">
                            {{ item.schedule?.class_room?.name }}
                          </div>
                        </div>
                        <div class="flex items-center">
                          <img
                            class="w-10 h-10 rounded-full mr-4"
                            :src="item.schedule?.link_poster"
                            alt="Avatar of Jonathan Reinink"
                          />
                          <div class="text-sm">
                            <p class="text-gray-900 leading-none">
                              {{ item.status }}
                            </p>
                            <p class="text-gray-600">
                              {{ item.schedule?.created_at }}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayoutCommittee>
  </div>
</template>
