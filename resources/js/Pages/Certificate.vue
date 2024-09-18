<script setup>
import AuthenticatedLayoutCommittee from "@/Layouts/AuthenticatedLayoutCommittee.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { initFlowbite, Modal } from "flowbite";
import Swal from "sweetalert2";
import TabMenuDetailParticipant from "@/Components/Committee/TabMenuDetailParticipant.vue";
import VueQrcode from "@chenfengyuan/vue-qrcode";
import { jsPDF } from "jspdf";
import html2canvas from "html2canvas";
const fullUrl = ref(window.location.origin);

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

</script>

<template>
  <Head title="Sertifikat" />
  <div>
    <div class="max-w-4xl mx-auto sm:px-4 lg:px-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-4 text-gray-900" id="pdf-content">
            <div class="relative overflow-x-auto">
              <div class="w-full h-full" style="border: 8px solid black">
                <div class="border-black border-1 m-4 h-auto">
                  <div class="grid grid-cols-2 gap-2">
                    <div class="flex flex-col p-4">
                      <div class="flex-1">
                        <h1 class="text-3xl font-bold mb-12">Madrasah MPJ</h1>
                        <span
                          class="bg-gray-900 text-white font-bold p-1 py-1 rounded-sm"
                          >{{ certificate[0].credential_id }}</span
                        >
                        <p class="mt-8">Diberikan kepada</p>
                        <h1 class="text-blue-400 font-bold text-xl">
                          {{ certificate[0].user?.name }}
                        </h1>
                        <p class="mt-2">Atas kelulusanya pada kelas</p>
                        <h1 class="text-blue-400 font-bold text-lg">
                          {{
                            certificate[0].user.submissions[0].schedule
                              .class_room.name
                          }}
                          {{
                            certificate[0].user.submissions[0].schedule
                              .category.name
                          }}
                        </h1>
                      </div>
                      <div class="mt-4">
                        <p class="mt-8 mb-2">
                          {{ certificate[0].formatted_created_at }}
                        </p>
                        <div class="w-20 pb-2">
                          <img
                            width="100%"
                            src="/mpj.svg"
                            alt="tanda tangan"
                          />
                        </div>
                      </div>
                      <p class="font-bold text-sm">
                        {{ certificate[0]?.head_organization.name }}
                      </p>
                      <p class="text-xs">Chief Executive Officer</p>
                      <p class="text-xs">Madrasah Media Pondok Jatim</p>
                    </div>
                    <div class="flex flex-col p-4">
                      <div class="flex-1 flex flex-col items-end">
                        <div class="mb-8 w-24">
                          <img
                            width="100%"
                            src="/mpj.svg"
                            alt="MPJ"
                            class="inline-block"
                          />
                        </div>
                      </div>
                      <div class="flex-1 flex flex-col items-end">
                        <div class="mb-8 w-24">
                          <img
                            width="100%"
                            :src="certificate[0].image"
                            :alt="certificate[0].user?.name"
                            class="inline-block"
                          />
                        </div>
                      </div>
                      <div class="flex flex-col items-end mt-auto">
                        <div>
                          <vue-qrcode
                            :value="`${fullUrl}/certificate/${certificate[0].credential_id}`"
                          ></vue-qrcode>
                        </div>
                        <div class="text-right">
                          <p class="font-bold text-sm">
                            Verifikasi Sertifikat
                          </p>
                          <p class="text-xs">
                            {{
                              fullUrl +
                              "/certificate/" +
                              certificate[0].credential_id
                            }}
                          </p>
                          <p class="text-xs">
                            Berlaku hingga
                            <span class="font-bold">{{
                              certificate[0].formatted_expired_at
                            }}</span>
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
</template>
