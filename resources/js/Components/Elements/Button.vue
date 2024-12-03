<script>
import classnames from 'classnames';
import TooltipV2 from "@/Components/Elements/TooltipV2.vue";
import { ref, computed } from "vue";

const showTooltip = ref(false);

const sizes = {
    xs: 'px-2.5 py-1.5 text-xs',
    sm: 'px-3 py-2 text-sm leading-4',
    md: 'px-4 py-2 text-sm',
    lg: 'px-4 py-2 text-base',
    xl: 'px-6 py-3 text-base',
}

const circularSizes = {
    xs: 'p-1',
    sm: 'p-1.5',
    md: 'p-2',
    lg: 'p-2',
    xl: 'p-3',
}

const colors = {
    primary: 'border-transparent text-white bg-primary-600 hover:bg-primary-700 transition ease-in-out duration-300',
    secondary: 'border-transparent text-white bg-boost-green-500 hover:bg-boost-green-600 transition ease-in-out duration-300',
    white: 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50 shadow-sm focus:ring-primary-500',
    gray: 'border-transparent text-gray-600 bg-gray-300 hover:bg-gray-400 focus:ring-gray-500',
    info: 'border-transparent text-white bg-info-600 hover:bg-info-700 focus:ring-info-500',
    success: 'border-transparent text-white bg-success-600 hover:bg-success-700 focus:ring-success-500',
    error: 'border-transparent text-white bg-error-600 hover:bg-error-700 focus:ring-error-500',
    warning: 'border-transparent text-white bg-warning-600 hover:bg-warning-700 focus:ring-warning-500',
    muted: 'border-transparent text-primary-700 bg-primary-100 hover:bg-primary-200 focus:ring-primary-500',
    indigo: 'border-transparent text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500',
    blue: 'border-transparent text-white bg-boost-blue-600 hover:bg-boost-blue-700 focus:ring-boost-blue-500',
    red: 'border-transparent text-white bg-boost-red-500 hover:bg-boost-red-700 focus:ring-boost-red-500',
    yellow: 'border-transparent text-yellow-700 bg-boost-yellow-400 bg-opacity-50 hover:bg-boost-yellow-300 focus:ring-boost-yellow-500',
    orange: 'border-transparent text-white bg-orange-600 hover:bg-orange-700 focus:ring-orange-500',
    green: 'border-transparent text-white bg-boost-green-500 hover:bg-boost-green-700 focus:ring-boost-green-500',
    pink: 'border-transparent text-white bg-pink-600 hover:bg-pink-700 focus:ring-pink-500',
    muted_gray: 'border-transparent text-gray-600 bg-gray-100 hover:bg-gray-200 focus:ring-gray-500',
    muted_blue: 'border-transparent text-boost-blue-700 bg-boost-blue-100 hover:bg-boost-blue-200 focus:ring-boost-blue-500',
    muted_green: 'border-transparent text-boost-green-700 bg-boost-green-100 hover:bg-boost-green-200 focus:ring-boost-green-500',
    muted_red: 'border-transparent text-boost-red-700 bg-boost-red-100 hover:bg-boost-red-200 focus:ring-boost-red-500',
    simple_blue: 'border-transparent text-primary-600 bg-transparent hover:text-primary-700 hover:bg-boost-blue-100 focus:ring-transparent',
    simple_pink: 'border-transparent text-pink-600 bg-transparent hover:text-pink-700 hover:bg-pink-100 focus:ring-transparent',
    simple_red: 'border-transparent text-red-600 bg-transparent hover:text-red-700 hover:bg-red-100 focus:ring-transparent',
}

export default {
    components: {
        TooltipV2
    },
    props: {
        size: {
            default: 'md',
            type: String
        },
        type: {
            default: 'submit',
            type: String
        },
        color: {
            default: 'primary',
            type: String
        },
        rounded: {
            default: false,
            type: Boolean
        },
        circular: {
            default: false,
            type: Boolean
        },
        disabled: {
            default: false,
            type: Boolean
        },
        tooltipText: {
            default: '',
            type: String
        }
    },
    computed: {
        className() {
            return classnames(
                'UIButton border inline-flex items-center justify-center font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 relative',
                this.circular ? circularSizes[this.responsiveSize] : sizes[this.responsiveSize],
                colors[this.color],
                this.responsiveSize,
                {
                    'rounded-full': this.rounded || this.circular,
                    'rounded-md': !this.rounded && !this.circular,
                    'opacity-50 cursor-default': this.disabled
                }
            )
        }
    },
    setup(props) {
        const showTooltip = ref(false);
        const windowWidth = ref(window.innerWidth);

        const responsiveSize = computed(() => {
            if (windowWidth.value < 640) return 'sm';
            else return props.size;
        });

        return { showTooltip, responsiveSize };
    }
}
</script>

<template>
    <button :type="type" :class="className" :disabled="disabled" v-tooltip="{
        content: tooltipText ? `<div class='text-xs'>${tooltipText}</div>` : '', html: true}">
        <slot></slot>
    </button>
</template>

<style>
    .v-popper--theme-tooltip .v-popper__inner {
        background: rgb(31 41 55)!important;
    }
    .v-popper__arrow-container {
        display: none;
    }
</style>
