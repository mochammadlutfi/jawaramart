<template>
</template>

<script>
export default {
    name : "flash-message",
    props: {
        image: {
            required: true
        },

    },
    data() {
        return {
            imgSrc: "/public/images/placeholder.png",
            cropImg: "",
            data: null,
            filename: "",
            mimeType: "",
            modalOpen: false,
        };
    },
    methods: {
      makeToast(append = false) {
        this.toastCount++
        this.$bvToast.toast(`This is toast number ${this.toastCount}`, {
          title: 'BootstrapVue Toast',
          autoHideDelay: 5000,
          appendToast: append
        })
      }
    }
};
</script>