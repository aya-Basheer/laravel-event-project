<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Admin Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="p-4 border rounded shadow">
        <h2 class="font-semibold">Total Users</h2>
        <p class="text-2xl">{{ stats.users }}</p>
      </div>
      <div class="p-4 border rounded shadow">
        <h2 class="font-semibold">Total Events</h2>
        <p class="text-2xl">{{ stats.events }}</p>
      </div>
      <div class="p-4 border rounded shadow">
        <h2 class="font-semibold">Tickets Sold</h2>
        <p class="text-2xl">{{ stats.tickets }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    stats: Object
  }
}
</script>
