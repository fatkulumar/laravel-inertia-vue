<script setup>
import AuthenticatedLayoutParticipant from "@/Layouts/AuthenticatedLayoutParticipant.vue";

const props = defineProps({
  schedule: {
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
  <Head title="Event Tersedia" />
  <div>
    <AuthenticatedLayoutParticipant>
      <!-- {{ props.schedule?.regional?.schedule[0]?.status }} -->
      <!-- {{ props.schedule?.regional?.schedule }} -->
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <div
                  class="flex gap-2 items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-900"
                  v-for="(item, index) in props.schedule"
                  :key="index"
                >
                  <a
                    href="#"
                    class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700 w-full"
                  >
                    <img
                      class="object-cover w-full rounded-t-lg h-96 md:h-auto md:w-48 md:rounded-none md:rounded-s-lg"
                      src="/docs/images/blog/image-4.jpg"
                      alt=""
                    />
                    <div
                      class="flex flex-col justify-between p-4 leading-normal"
                    >
                      <h5
                        class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                      >
                        {{ item.class_room?.name }} | {{ item.category?.name }}
                      </h5>
                      <div class="flex gap-2 bg-gray-400 p-1 rounded-sm">
                        <p>{{ item.start_date_class }}</p>
                        <p> - </p>
                        <p>{{ item.end_date_class }}</p>
                      </div>
                      <p
                        class="mb-3 font-normal text-gray-700 dark:text-gray-400"
                      >
                        {{ item.facility }}
                      </p>
                      <p
                      class="mb-3 font-normal text-gray-700 dark:text-gray-400"
                    >
                      {{ item.benefit }}
                    </p>
                    </div>
                  </a>

                  <a
                    href="#"
                    class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700"
                  >
                    <img
                      class="object-cover w-full rounded-t-lg h-96 md:h-auto md:w-48 md:rounded-none md:rounded-s-lg"
                      src="/docs/images/blog/image-4.jpg"
                      alt=""
                    />
                    <div
                      class="flex flex-col justify-between p-4 leading-normal"
                    >
                      <h5
                        class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                      >
                        Noteworthy technology acquisitions 2021
                      </h5>
                      <p
                        class="mb-3 font-normal text-gray-700 dark:text-gray-400"
                      >
                        Here are the biggest enterprise technology acquisitions
                        of 2021 so far, in reverse chronological order.
                      </p>
                    </div>
                  </a>

                  <a
                    href="#"
                    class="flex flex-col items-center bg-white border border-gray-200 rounded-lg shadow md:flex-row md:max-w-xl hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700"
                  >
                    <img
                      class="object-cover w-full rounded-t-lg h-96 md:h-auto md:w-48 md:rounded-none md:rounded-s-lg"
                      src="/docs/images/blog/image-4.jpg"
                      alt=""
                    />
                    <div
                      class="flex flex-col justify-between p-4 leading-normal"
                    >
                      <h5
                        class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                      >
                        Noteworthy technology acquisitions 2021
                      </h5>
                      <p
                        class="mb-3 font-normal text-gray-700 dark:text-gray-400"
                      >
                        Here are the biggest enterprise technology acquisitions
                        of 2021 so far, in reverse chronological order.
                      </p>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayoutParticipant>
  </div>
</template>
