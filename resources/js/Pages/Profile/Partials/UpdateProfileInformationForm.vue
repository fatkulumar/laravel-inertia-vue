<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import axios from "axios";

defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
  regionals: {
    type: Object,
  },
  regencyRegional: {
    type: Object,
  },
  user: {
    type: Object,
  },
});

const user = usePage().props.user;
const errors = usePage().props.errors;
const my_regency_regional = usePage().props.regencyRegional;

const form = useForm({
  name: user[0].name,
  email: user[0].email,
  image: user[0].profile?.image,
  regional_id: user[0].profile?.regional?.id,
  regency_regional_id: user[0].profile?.regency_regional?.id,
  gender: user[0].profile ? user[0].profile?.gender : null,
  address: user[0].profile ? user[0].profile?.address : null,
  hp: user[0].profile ? user[0].profile?.hp : null,
});

const regencyRegionals = ref(my_regency_regional);
const chainedRegencyRegional = async (regionalId) => {
    await axios
    .get(`/profile/regency_regional/${regionalId}`)
    .then((response) => {
        regencyRegionals.value = response.data;
    })
    .catch((error) => console.error(error));
};

const previewImage = ref(user[0].profile?.image);
const uploadImage = (e) => {
  const image = e.target.files[0];
  if (
    image.type == "image/png" ||
    image.type == "image/jpg" ||
    image.type == "image/jpeg"
  ) {
    const reader = new FileReader();
    reader.readAsDataURL(image);
    reader.onload = (e) => {
      previewImage.value = e.target.result;
      form.image = image;
    };
  } else {
    form.image = null;
    toast("warning", "Harus Format Gambar");
  }
};

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

function hideAlert(idElemet) {
  let successMessage = document.getElementById(idElemet);

  if (successMessage) {
    setTimeout(() => {
      successMessage.style.transition = "height 300ms ease-in-out";
      successMessage.style.height = "0";
      setTimeout(() => {
        successMessage.style.display = "none";
      }, 300); // Waktu yang sama dengan durasi animasi slide up
    }, 1); // Delay sebelum animasi dimulai
  }
}
</script>

<template>
  <section>
    <div
      class="text-red-600 text-sm ml-2"
      v-for="(error, index) in errors"
      :key="index"
    >
      *{{ error }}
    </div>
    <div v-if="$page.props.flash.success">
      <Transition
        enter-active-class="transition ease-in-out"
        enter-from-class="opacity-0"
        leave-active-class="transition ease-in-out"
        leave-to-class="opacity-0"
      >
        <div v-if="form.recentlySuccessful" class="text-sm text-gray-600">
          <div
            id="alert-border-1"
            class="flex items-center p-4 mb-4 text-blue-800 border-t-4 border-blue-300 bg-blue-50 dark:text-blue-400 dark:bg-gray-800 dark:border-blue-800"
            role="alert"
          >
            <svg
              class="flex-shrink-0 w-4 h-4"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"
              />
            </svg>
            <div class="ms-3 text-sm font-medium">
              {{ $page.props.flash.success }}
            </div>
            <button
              id="button-alert-border-1"
              type="button"
              @click="hideAlert('alert-border-1')"
              class="ms-auto -mx-1.5 -my-1.5 bg-blue-50 text-blue-500 rounded-lg focus:ring-2 focus:ring-blue-400 p-1.5 hover:bg-blue-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-blue-400 dark:hover:bg-gray-700"
              data-dismiss-target="#alert-border-1"
              aria-label="Close"
            >
              <span class="sr-only">Dismiss</span>
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
            </button>
          </div>
        </div>
      </Transition>
    </div>

    <div v-if="$page.props.flash.error">
      <Transition
        enter-active-class="transition ease-in-out"
        enter-from-class="opacity-0"
        leave-active-class="transition ease-in-out"
        leave-to-class="opacity-0"
      >
        <div v-if="form.recentlySuccessful" class="text-sm text-gray-600">
          <div
            id="alert-border-2"
            class="flex items-center p-4 mb-4 text-red-800 border-t-4 border-red-300 bg-red-50 dark:text-red-400 dark:bg-gray-800 dark:border-red-800"
            role="alert"
          >
            <svg
              class="flex-shrink-0 w-4 h-4"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"
              />
            </svg>
            <div class="ms-3 text-sm font-medium">
              {{ $page.props.flash.error }}
            </div>
            <button
              type="button"
              class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-gray-700"
              data-dismiss-target="#alert-border-2"
              aria-label="Close"
            >
              <span class="sr-only">Dismiss</span>
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
            </button>
          </div>
        </div>
      </Transition>
    </div>

    <header>
      <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>

      <p class="mt-1 text-sm text-gray-600">
        Update your account's profile information and email address.
      </p>
    </header>
    <form
      @submit.prevent="form.post(route('profile.update'))"
      class="mt-6 space-y-6"
    >
      <div class="w-5/12">
        <img :src="previewImage" alt="" />
      </div>
      <div>
        <InputLabel for="image" value="Photo" />

        <input type="file" accept="image/*" @change="uploadImage" />

        <InputError class="mt-2" :message="form.errors.image" />
      </div>

      <div class="grid grid-cols-2 gap-2">
        <div>
          <InputLabel for="name" value="Name" />

          <TextInput
            id="name"
            type="text"
            class="mt-1 block w-full"
            v-model="form.name"
            required
            autofocus
            autocomplete="name"
          />

          <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div>
          <InputLabel for="email" value="Email" />

          <TextInput
            id="email"
            type="email"
            class="mt-1 block w-full"
            v-model="form.email"
            required
            autocomplete="username"
          />

          <InputError class="mt-2" :message="form.errors.email" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-2">
        <div>
          <InputLabel for="regional_id" value="Regional" />

          <select
            @change="chainedRegencyRegional(form.regional_id)"
            id="regional_id"
            class="mt-1 block w-full"
            v-model="form.regional_id"
            required
            autofocus
            autocomplete="regional_id"
          >
            <option :selected="form.regional_id == null" value="">
              Pilih Regional
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
          <InputError class="mt-2" :message="form.errors.regency_regional_id" />
        </div>

        <div>
          <InputLabel for="regency_regional_id" value="Kabuptan" />
          <select
          id="regency_regional_id"
          class="mt-1 block w-full"
          v-model="form.regency_regional_id"
          required
          autofocus
          autocomplete="regency_regional_id"
        >
         <option :selected="form.regency_regional_id == null" value="">Pilih Kabupaten</option>
          <option
            v-for="(item, index) in regencyRegionals"
            :key="index"
            :value="item.id"
            :selected="item.id == form.regency_regional_id"
          >
            {{ item.regency }}
          </option>
        </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-2">
        <div>
          <InputLabel for="hp" value="HP" />

          <TextInput
            id="hp"
            type="number"
            class="mt-1 block w-full"
            v-model="form.hp"
            required
            autofocus
            autocomplete="hp"
          />

          <InputError class="mt-2" :message="form.errors.hp" />
        </div>

        <div>
          <InputLabel for="gender" value="Jenis Kelamin" />
          <select
            id="gender"
            class="mt-1 block w-full"
            v-model="form.gender"
            required
            autofocus
          >
            <option value="" :selected="form.gender == null">Jenis Kelamin</option>
            <option :selected="form.gender == 'laki-laki'" value="laki-laki">
              Laki-Laki
            </option>
            <option :selected="form.gender == 'perempuan'" value="perempuan">
              Perempuan
            </option>
          </select>

          <InputError class="mt-2" :message="form.errors.gender" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-2">
        <div>
          <InputLabel for="address" value="address" />

          <textarea
            placeholder="Alamat Rumah Kamu"
            id="address"
            class="mt-1 block w-full"
            v-model="form.address"
            required
            autofocus
            autocomplete="address"
          />

          <InputError class="mt-2" :message="form.errors.address" />
        </div>

        <!-- <div>
          <InputLabel for="gender" value="Jenis Kelamin" />

          <select
            id="gender"
            type="text"
            class="mt-1 block w-full"
            v-model="form.gender"
            required
            autofocus
            autocomplete="gender"
          >

              <option value="">Jenis Kelamin</option>
              <option :selected="form.gender == 'laki-laki'" value="laki-laki">Laki-Laki</option>
              <option :selected="form.gender == 'perempuan'" value="perempuan">Perempuan</option>
          </select>

          <InputError class="mt-2" :message="form.errors.email" />
        </div> -->
      </div>

      <div v-if="mustVerifyEmail && user.email_verified_at === null">
        <p class="text-sm mt-2 text-gray-800">
          Your email address is unverified.
          <Link
            :href="route('verification.send')"
            method="post"
            as="button"
            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Click here to re-send the verification email.
          </Link>
        </p>

        <div
          v-show="status === 'verification-link-sent'"
          class="mt-2 font-medium text-sm text-green-600"
        >
          A new verification link has been sent to your email address.
        </div>
      </div>

      <div class="flex items-center gap-4">
        <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

        <Transition
          enter-active-class="transition ease-in-out"
          enter-from-class="opacity-0"
          leave-active-class="transition ease-in-out"
          leave-to-class="opacity-0"
        >
          <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
            Saved.
          </p>
        </Transition>
      </div>
    </form>
  </section>
</template>
