<script setup>
import AuthenticatedLayoutCommittee from "@/Layouts/AuthenticatedLayoutCommittee.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import { Modal } from "flowbite";
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

const urlPath = window.location.pathname;
const segments = urlPath.split("/");
const idSubmissionLastSegment = segments.pop() || segments.pop();

function downloadCertificate() {
  //   document.getElementById("pdf-content").style.display = "block";
  const pdfContent = document.getElementById("pdf-content");

  html2canvas(pdfContent).then((canvas) => {
    const imgData = canvas.toDataURL("image/png");
    const pdf = new jsPDF({
      orientation: "landscape",
      unit: "mm",
      format: "a4",
    });
    const imgWidth = 298;
    const imgHeight = (canvas.height * imgWidth) / canvas.width;
    pdf.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight);
    pdf.save("download.pdf");
  });
  //   document.getElementById("pdf-content").style.display = "none";
}

const printCertificate = () => {
  const element = document.getElementById("pdf-content");
  html2canvas(element).then((canvas) => {
    const imgData = canvas.toDataURL("image/png");
    const pdf = new jsPDF({
      orientation: "landscape", // atau "landscape"
      unit: "mm",
      format: "a4",
    });

    const imgWidth = 298; // Lebar kertas A4
    const pageHeight = 295; // Tinggi kertas A4
    const imgHeight = (canvas.height * imgWidth) / canvas.width;
    let heightLeft = imgHeight;

    let position = 0;

    pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
    heightLeft -= pageHeight;

    while (heightLeft >= 0) {
      position = heightLeft - imgHeight;
      pdf.addPage();
      pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
      heightLeft -= pageHeight;
    }

    pdf.autoPrint(); // Menyiapkan PDF untuk dicetak
    window.open(pdf.output("bloburl"), "_blank"); // Membuka PDF di tab baru
  });
};
</script>

<template>
  <Head title="Sertifikat" />
  <div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900" id="pdf-content">
          <div class="relative overflow-x-auto">
            <div class="w-full h-full" style="border: 16px solid black">
              <div class="border-black border-2 m-8 h-auto">
                <div class="grid grid-cols-2 gap-4">
                  <div class="flex flex-col p-8">
                    <div class="flex-1">
                      <h1 class="text-5xl font-bold mb-24">Madrasah MPJ</h1>
                      <span
                        class="bg-gray-900 text-white font-bold p-1 py-2 rounded-sm"
                        >{{ certificate[0].credential_id }}</span
                      >
                      <p class="mt-16">Diberikan kepada</p>
                      <h1 class="text-blue-400 font-bold text-3xl">
                        {{ certificate[0].user?.name }}
                      </h1>
                      <p class="mt-3">Atas kelulusanya pada kelas</p>
                      <h1 class="text-blue-400 font-bold text-xl">
                        {{
                          certificate[0].user.submissions[0].schedule.class_room
                            .name
                        }}
                        {{
                          certificate[0].user.submissions[0].schedule.category
                            .name
                        }}
                      </h1>
                    </div>
                    <div class="mt-5">
                      <p class="mt-16 mb-3">
                        {{ certificate[0].formatted_created_at }}
                      </p>
                      <div class="w-24 py-2">
                        <img width="100%" src="/mpj.svg" alt="tanda tangan" />
                      </div>
                    </div>
                    <p class="font-bold">
                      {{ certificate[0]?.head_organization.name }}
                    </p>
                    <p class="text-xs">Chief Executive Officer</p>
                    <p class="text-xs">Madrasah Media Pondok Jatim</p>
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
                    <div class="flex flex-col items-end mt-auto">
                        <div>
                          <vue-qrcode :value="`${fullUrl}/certificate/${certificate[0].credential_id}`"></vue-qrcode>
                        </div>
                        <div class="text-right">
                          <p class="font-bold">Verifikasi Sertifikat</p>
                          <p class="text-xs"> {{ fullUrl + "/certificate/" + certificate[0].credential_id }}</p>
                          <p class="text-xs">Berlaku hingga <span class="font-bold">{{ certificate[0].formatted_expired_at }}</span></p>
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
