<template>
    <input
        :disabled="disabled"
        :value="modelValue"
        :type="type"
        :id="id ?? inputId"
        @input="$emit('update:modelValue', $event.target.value)"
        class="block w-full sm:text-sm rounded-md cursor-pointer border"
        :class="[
            disabled ? 'bg-gray-100' : '',
            inputError ? 'border-error-300 text-error-900 placeholder-error-300 focus:ring-error-500 focus:border-error-500 pr-10' : 'border-gray-300 focus:ring-primary-500 focus:border-primary-500 pl-2',
            icon ? 'indent-[50px]' : '',
            styleMode === 'light' ? 'bg-gray-50 border-0 border-b-2' : '',
        ]"
        v-bind="$attrs"
        :min="min"
        :max="max"
        onfocus="this.showPicker()"
    />
    <div class="absolute inset-y-0 right-0 items-center pr-3 flex pointer-events-none" v-if="inputError">
        <ExclamationCircleIcon class="h-5 w-5 text-error-500" v-if="inputError"/>
    </div>
</template>
<script>
import { ExclamationCircleIcon } from '@heroicons/vue/24/solid'


export default {
    components: {
        ExclamationCircleIcon
    },
    props: {
        modelValue: {
            type: [String, Number],
            default: '',
            required: false
        },
        type: {
            type: String,
            default: 'text',
            required: false
        },
        id: {
            type: String,
            default: null
        },
        disabled: {
            type: Boolean,
            default: false,
            required: false
        },
        icon: {
            type: Object,
            required: false
        },
        min: {
            type: String,
            required: false
        },
        max: {
            type: String,
            required: false
        },
        styleMode: {
            type: String,
            default: 'normal',
            required: false
        }
    },
    inject: {
        inputId: {
            default: null
        },
        inputError: {
            default: false
        },
    }
}
</script>
