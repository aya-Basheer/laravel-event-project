<template>
  <div class="p-6">
    <h1 class="text-3xl font-bold mb-6 text-teal-700">الفعاليات المتاحة</h1>

    <!-- البحث / الفلترة -->
    <div class="mb-4 flex flex-col md:flex-row md:items-center md:space-x-4">
      <input
        v-model="search"
        type="text"
        placeholder="ابحث عن فعالية..."
        class="border p-2 rounded w-full md:w-1/3 mb-2 md:mb-0"
      />
      <select v-model="filterStatus" class="border p-2 rounded w-full md:w-1/4">
        <option value="">جميع الحالات</option>
        <option value="draft">مسودة</option>
        <option value="published">منشورة</option>
        <option value="cancelled">ملغاة</option>
      </select>
    </div>

    <!-- قائمة الفعاليات -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="event in filteredEvents"
        :key="event.id"
        class="bg-white shadow-md rounded p-4 border border-gray-200"
      >
        <h2 class="font-bold text-xl text-teal-600">{{ event.title }}</h2>
        <p class="text-gray-600 mt-2">{{ event.description }}</p>
        <p class="text-gray-500 mt-1 text-sm">
          {{ event.start_date }} - {{ event.end_date }}
        </p>
        <p class="text-gray-500 text-sm mt-1">الحالة: {{ event.status }}</p>

        <button
          @click="buyTicket(event.id)"
          class="mt-4 w-full bg-teal-500 hover:bg-teal-600 text-white font-semibold py-2 rounded"
        >
          شراء التذكرة
        </button>
      </div>
    </div>

    <!-- إشعارات -->
    <Toast v-for="toast in toasts" :key="toast.id" :message="toast.message" />
  </div>
</template>

<script>
import axios from "axios";
import Toast from "@/Components/Toast.vue";

export default {
  components: { Toast },
  data() {
    return {
      events: [],
      search: "",
      filterStatus: "",
      toasts: [],
    };
  },
  computed: {
    filteredEvents() {
      return this.events
        .filter((e) =>
          e.title.toLowerCase().includes(this.search.toLowerCase())
        )
        .filter((e) =>
          this.filterStatus ? e.status === this.filterStatus : true
        );
    },
  },
  mounted() {
    this.fetchEvents();
  },
  methods: {
    fetchEvents() {
      axios.get("/api/events").then((res) => {
        this.events = res.data;
      });
    },
    buyTicket(eventId) {
      axios
        .post("/api/tickets", { event_id: eventId })
        .then(() => {
          this.toasts.push({
            id: Date.now(),
            message: "تم شراء التذكرة بنجاح",
          });
        })
        .catch(() => {
          this.toasts.push({
            id: Date.now(),
            message: "حدث خطأ أثناء الشراء",
          });
        });
    },
  },
};
</script>

<style scoped>
/* يمكنك إضافة تأثيرات hover أو animations هنا */
</style>
