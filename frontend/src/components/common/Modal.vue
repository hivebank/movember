<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="modelValue" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
          <!-- Backdrop -->
          <div
            class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
            @click="close"
          ></div>

          <!-- Modal panel -->
          <div
            class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
          >
            <div class="absolute top-0 right-0 pt-4 pr-4">
              <button
                type="button"
                class="text-gray-400 hover:text-gray-500 focus:outline-none"
                @click="close"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div class="sm:flex sm:items-start">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3 v-if="title" class="text-lg font-medium leading-6 text-gray-900 mb-4">
                  {{ title }}
                </h3>
                <div class="mt-2">
                  <slot></slot>
                </div>
              </div>
            </div>

            <div v-if="$slots.footer" class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
              <slot name="footer"></slot>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
defineProps({
  modelValue: {
    type: Boolean,
    required: true,
  },
  title: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue'])

const close = () => {
  emit('update:modelValue', false)
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
