<template>
    <slot></slot>
</template>
<script>

export default {
    components: {

    },

    methods: {
        flash() {
            ['info', 'success', 'error', 'warning'].map(type => {
                if (this.$page.props.flash[type])
                    this.$notify({ type, title: this.$page.props.flash[type] }, 3000)
            })
        }
    },

    mounted() {
        this.flash()
    },

    watch: {
        "$page.props.flash": {
            deep: true,
            handler() {
                this.flash()
            }
        }
    }
}
</script>
