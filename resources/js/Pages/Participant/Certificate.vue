<script setup>
import AuthenticatedLayoutParticipant from "@/Layouts/AuthenticatedLayoutParticipant.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { onMounted, ref, defineComponent } from "vue";
import { Modal } from "flowbite";
import Swal from "sweetalert2";
import VueQrcode from "@chenfengyuan/vue-qrcode";
import TabMenuDetailParticipant from "@/Components/Participant/TabMenuDetailParticipant.vue";
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
  document.getElementById("pdf").style.display = "block";
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
  document.getElementById("pdf-content").style.display = "none";
}

const printCertificate = () => {
  document.getElementById("pdf").style.display = "block";
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
    document.getElementById("pdf").style.display = "none";
    window.open(pdf.output("bloburl"), "_blank"); // Membuka PDF di tab baru
  });
};

const form = useForm({
  credential_id: props.certificate[0]?.credential_id,
  image: "",
});

function resetForm() {
  form.image = null;
  previewImage.value = null;
}

const uploadFiles = ref(null);

const clickUpload = () => {
  uploadFiles.value.click();
};

const previewImage = ref(props.certificate[0]?.image);
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
      form.post("/participant/participant/change-image", {
        preserveScroll: true,
        onSuccess: () => {
          resetForm();
          toast("success", "Data Berhasil Ditambah");
        },
      });
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

<style scoped>
@media print {
    .bg-gray-900 {
      margin-bottom: 10px; /* Sesuaikan nilai ini dengan kebutuhan Anda */
    }
  }
</style>

<template>
  <Head title="Sertifikat" />
  <div>
    <AuthenticatedLayoutParticipant>
      <template #headerTitle> Sertifikat </template>
      <template #header>
        <TabMenuDetailParticipant :id="idSubmissionLastSegment" />
      </template>
      <div class="py-12">
        <div class="flex justify-center gap-1 pb-2">
          <button
            @click="downloadCertificate()"
            type="button"
            class="px-2 rounded-md bg-blue-500 text-white"
          >
            Download
          </button>
          <button
            @click="printCertificate()"
            type="button"
            class="px-2 rounded-md bg-red-500 text-white"
          >
            Cetak
          </button>

          <input
            ref="uploadFiles"
            accept="image/*"
            type="file"
            hidden
            @change="uploadImage"
          />

          <!-- Custom Button -->
          <button
            class="p-2 bg-yellow-600 text-white hover:opacity-75 rounded-md focus:outline-none"
            @click="clickUpload"
          >
            Ganti Foto
          </button>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 hidden" id="pdf">
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
                            class="bg-gray-900 text-white font-bold rounded-sm pb-4 px-2"
                            >{{ certificate[0].credential_id }}</span
                          >
                          <p class="mt-16">Diberikan kepada</p>
                          <h1 class="text-blue-400 font-bold text-3xl">
                            {{ certificate[0].user?.name }}
                          </h1>
                          <p class="mt-3">Atas kelulusanya pada kelas</p>
                          <h1 class="text-blue-400 font-bold text-xl">
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
                        <div class="mt-5">
                          <p class="mt-16 mb-3">
                            {{ certificate[0].formatted_created_at }}
                          </p>
                          <div class="w-24 pt-2">
                            <img
                              width="100%"
                              src="/mpj.svg"
                              alt="tanda tangan"
                            />
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
                              :src="previewImage"
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
                            <p class="font-bold">Verifikasi Sertifikat</p>
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
                              :src="previewImage"
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
    </AuthenticatedLayoutParticipant>
  </div>
</template>
