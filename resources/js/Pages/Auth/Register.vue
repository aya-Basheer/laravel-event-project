<template>
  <div class="max-w-md mx-auto mt-20">
    <h1 class="text-2xl font-bold mb-6">Register</h1>
    <form @submit.prevent="submit">
      <input type="text" v-model="form.name" placeholder="Name" class="border p-2 mb-3 w-full"/>
      <input type="email" v-model="form.email" placeholder="Email" class="border p-2 mb-3 w-full"/>
      <input type="password" v-model="form.password" placeholder="Password" class="border p-2 mb-3 w-full"/>
      <input type="password" v-model="form.password_confirmation" placeholder="Confirm Password" class="border p-2 mb-3 w-full"/>
      <select v-model="form.role" class="border p-2 mb-3 w-full">
        <option value="attendee">Attendee</option>
        <option value="organizer">Organizer</option>
      </select>
      <button type="submit" class="bg-teal-500 text-white px-4 py-2 rounded w-full">Register</button>
    </form>
  </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia';
export default {
  data() {
    return {
      form: { name: '', email: '', password: '', password_confirmation: '', role: 'attendee' }
    }
  },
  methods: {
    submit() {
      Inertia.post('/register', this.form);
    }
  }
}
</script>
