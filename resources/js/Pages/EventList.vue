<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">قائمة الفعاليات</h1>
    <ul>
      <li v-for="event in events" :key="event.id" class="mb-2 p-2 border rounded">
        <h2 class="font-semibold">{{ event.title }}</h2>
        <p>{{ event.description }}</p>
        <p>تاريخ البدء: {{ event.start_date }}</p>
        <p>تاريخ الانتهاء: {{ event.end_date }}</p>
      </li>
    </ul>
  </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'

export default {
  props: {
    events: Array
  },
}
</script>
