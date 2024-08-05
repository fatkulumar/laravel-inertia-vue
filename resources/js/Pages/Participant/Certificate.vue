<script setup>
import AuthenticatedLayoutParticipant from "@/Layouts/AuthenticatedLayoutParticipant.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { onMounted, ref, defineComponent } from "vue";
import { Modal } from "flowbite";
import Swal from "sweetalert2";
import VueQrcode from "@chenfengyuan/vue-qrcode";
import TabMenuDetailParticipant from "@/Components/Participant/TabMenuDetailParticipant.vue";

onMounted(() => {
  initFlowbite();
});

const props = defineProps({
  certificate: {
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

const urlPath = window.location.pathname;
const segments = urlPath.split("/");
const idSubmissionLastSegment = segments.pop() || segments.pop();
</script>

<template>
  <Head title="Sertifikat" />
  <div>
    <AuthenticatedLayoutParticipant>
      <template #headerTitle> Sertifikat </template>
      <template #header>
        <TabMenuDetailParticipant :id="idSubmissionLastSegment" />
      </template>
      <div class="py-12">
        <div class="flex justify-center">
          <button
            @click="print('printMe')"
            type="button"
            class="px-2 rounded-md bg-blue-500"
          >
            Cetak
          </button>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="relative overflow-x-auto">
                <div class="w-full h-auto" style="border: 16px solid black">
                  <div class="border-black border-2 m-8 h-auto">
                    <div id="printMe" class="grid grid-cols-2 gap-4">
                      <div class="flex flex-col p-8">
                        <div class="flex-1">
                          <h1 class="text-5xl font-bold mb-16">Madrasah</h1>
                          <span
                            class="bg-gray-900 text-white font-bold p-1 pt-2 rounded-sm"
                            >RVZKOK6WDDMD</span
                          >
                          <p class="mt-3">Diberikan kepada</p>
                          <h1 class="text-blue-400 font-bold text-3xl">
                            Fatkul Umar
                          </h1>
                          <p class="mt-3">Atas kelulusanya pada kelas</p>
                          <h1 class="text-blue-400 font-bold text-xl">
                            Desain Grafis Intermediate 1
                          </h1>
                          <p class="mt-16">04 Mei 2024</p>
                        </div>
                        <div class="w-24">
                          <img width="100%" src="/mpj.svg" alt="tanda tangan" />
                        </div>
                        <p class="font-bold">Ahmad Tajudin Zahro'u</p>
                        <p class="text-xs">Chief Executive Officer</p>
                        <p class="text-xs">Madrasah</p>
                      </div>
                      <div class="flex flex-col p-8">
                        <div class="flex-1 flex flex-col items-end">
                          <div class="mb-16 w-32">
                            <img
                              width="100%"
                              src="/mpj.svg"
                              alt="MPJ"
                              class="inline-block"
                            />
                          </div>
                        </div>
                        <div class="flex-1 flex flex-col items-end">
                          <div class="mb-16 w-32">
                            <img
                              width="100%"
                              src="/mpj.svg"
                              alt="MPJ"
                              class="inline-block"
                            />
                          </div>
                        </div>
                        <div class="flex justify-end">
                          <vue-qrcode value="https://dicoding.com"></vue-qrcode>
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
    </AuthenticatedLayoutParticipant>
  </div>
</template>
