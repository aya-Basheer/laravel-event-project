<template>
  <div class="bg-white shadow-md rounded p-4 border border-gray-200">
    <h2 class="font-bold text-xl text-teal-600">{{ event.title }}</h2>
    <p class="text-gray-600 mt-2">{{ event.description }}</p>
    <p class="text-gray-500 mt-1 text-sm">{{ event.start_date }} - {{ event.end_date }}</p>

    <div class="mt-4 flex justify-end space-x-2">
      <button @click="$emit('edit', event)" class="bg-blue-500 text-white px-3 py-1 rounded">تعديل</button>
      <button @click="$emit('delete', event.id)" class="bg-red-500 text-white px-3 py-1 rounded">حذف</button>
    </div>
  </div>
</template>

<script>
export default {
  props: { event: Object },
};
</script>
