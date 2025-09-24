<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Organizer Dashboard</h1>
    <button @click="$inertia.visit('/organizer/events/create')" class="bg-teal-500 text-white px-4 py-2 rounded mb-4">Create Event</button>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <EventCard v-for="event in events" :key="event.id" :event="event"/>
    </div>
  </div>
</template>

<script>
import EventCard from '@/Components/EventCard.vue';
export default {
  props: {
    events: Array
  },
  components: { EventCard }
}
</script>
