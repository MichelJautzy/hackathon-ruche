<script setup>
import { useForm } from '@inertiajs/vue3';
import { defineOptions } from 'vue';
import Input from "@/Components/Form/Input.vue";
import Button from "@/Components/Elements/Button.vue";

defineOptions({ layout: BaseLayout });

const form = useForm({
  barcode: ''
});

const submit = () => {
  form.get(route('hackathon.schema', { barcode: form.barcode }));
};
</script>

<template>
  <div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center sm:py-12">
    <img src="/hackathon.png" class="max-w-[500px] justify-center mx-auto mb-6" />
    <div class="relative py-3 w-full max-w-[500px] justify-center mx-auto">
      <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
        <div class="max-w-md mx-auto">
          <div v-if="form.processing"
               class="absolute top-0 left-0 w-full h-full flex items-center justify-center bg-white bg-opacity-80">
            <img src="/rocket.gif" class=" z-10" alt="Loading..." />
          </div>
          <form @submit.prevent="submit" class="space-y-6">
            <Input
                v-model="form.barcode"
                type="text"
                placeholder="Enter barcode"
                class="w-full"
                required
            />
            <div class="relative">
              <Button
                  type="submit"
                  color="primary"
                  class="w-full"
                  :disabled="form.processing"
              >
                Search
              </Button>

              <!-- Loading animation -->

            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>