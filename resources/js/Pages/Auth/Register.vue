<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from "axios";
import { ref } from 'vue';

const props = defineProps({
    regionals: {
        type: Object,
        default: () => ({}),
    },
});


const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    regional_id: '',
    regency_regional_id: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const regencyRegionals = ref([]);
const chainedRegencyRegional = async (regionalId) => {
    await axios
    .get(`/profile/regency_regional/${regionalId}`)
    .then((response) => {
        regencyRegionals.value = response.data;
    })
    .catch((error) => console.error(error));
};

</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div class="flex flex-wrap -mx-2">
                <!-- Kolom 1 -->
                <div class="w-full md:w-1/2 px-2">
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

                    <div class="mt-4">
                        <InputLabel for="password" value="Password" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="regional_id" value="Regional" />
                        <select required @change="chainedRegencyRegional(form.regional_id)" name="regional_id" v-model="form.regional_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Pilih Regional</option>
                            <option v-for="item, index in props.regionals" :key="index" :value="item.id">
                                {{ item.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.regional_id" />
                    </div>
                </div>

                <!-- Kolom 2 -->
                <div class="w-full md:w-1/2 px-2">
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

                    <div class="mt-4">
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="regency_regional_id" value="Kabupaten" />
                        <select required name="regency_regional_id" v-model="form.regency_regional_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="">Pilih Kabupaten</option>
                            <option v-for="item, index in regencyRegionals" :key="index" :value="item.id">
                                {{ item.regency }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.regency_regional_id" />
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Sudah punya akun?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>

    </GuestLayout>
</template>
