<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
  regencyRegionals: {
    type: Object,
  },
  regionals: {
    type: Object,
  },
  user: {
    type: Object,
  },
});

const user = usePage().props.user;

const form = useForm({
  name: user[0].name,
  email: user[0].email,
  image: user[0].profile?.image,
  regional_id: user[0].profile?.regional?.id,
  regency_regional_id: user[0].profile?.regional?.regency_regional.id,
  gender: user[0].profile?.gender,
  address: user[0].profile?.address,
  hp: user[0].profile?.hp,
});

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
</script>

<template>
  <section>
    <header>
      <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>

      <p class="mt-1 text-sm text-gray-600">
        Update your account's profile information and email address.
      </p>
    </header>
{{ flash }}
    <form
      @submit.prevent="form.post(route('profile.update'))"
      class="mt-6 space-y-6"
    >
      <div class="w-5/12">
        <img :src="previewImage" alt="" />
      </div>
      <div>
        <InputLabel for="image" value="Photo" />

        <input type="file" @change="uploadImage" />

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
            id="regional_id"
            class="mt-1 block w-full"
            v-model="form.regional_id"
            required
            autofocus
            autocomplete="regional_id"
          >
            <option :selected="form.regional_id == null" value="null">
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
            <option :selected="form.regional_id == null" value="null">
              Pilih Regional
            </option>
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
            autocomplete="gender"
          >

              <option value="">Jenis Kelamin</option>
              <option :selected="form.gender == 'laki-laki'" value="laki-laki">Laki-Laki</option>
              <option :selected="form.gender == 'perempuan'" value="perempuan">Perempuan</option>
          </select>

          <InputError class="mt-2" :message="form.errors.gender" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-2">
        <div>
          <InputLabel for="address" value="address" />

          <textarea placeholder="Alamat Rumah Kamu"
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
