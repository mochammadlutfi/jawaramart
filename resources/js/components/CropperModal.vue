<script>
import VueCropper from 'vue-cropperjs';
import 'cropperjs/dist/cropper.css';
export default {
    name: 'Modal',
    components : {
        VueCropper,
    },
    props: {
        // eslint-disable-next-line vue/require-prop-types
        image: {
            required: true
        },

    },
    data() {
        return {
            imgSrc: '/assets/images/berserk.jpg',
            cropImg: '',
            data: null,
        };
    },
    methods: {
        setImage(e){
            const file = e.target.files[0];
            if (file.type.indexOf('image/') === -1) {
                alert('Please select an image file');
                return;
            }
            if (typeof FileReader === 'function') {
                const reader = new FileReader();
                reader.onload = (event) => {
                this.imgSrc = event.target.result;
                // rebuild cropperjs with the updated source
                this.$refs.cropper.replace(event.target.result);
                };
                reader.readAsDataURL(file);
            } else {
                alert('Sorry, FileReader API not supported');
            }
        },
        close() {
            this.$emit('close');
        },
    },
  };
</script>

<template>
    <transition name="modal-fade">
        <div class="modal-backdrop">
            <div class="modal" id="modal-large" tabindex="-1" aria-labelledby="modal-large"
                style="display: block; padding-right: 17px;" aria-modal="true" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title">{{ this.imageSource }}</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" @click="close" aria-label="Close">
                                        <i class="si si-close"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content">
                                <vue-cropper ref="cropper" :aspect-ratio="16 / 9" :src="imgSrc"/>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-alt-secondary" @click="close">Close</button>
                            <button type="button" class="btn btn-alt-success" @click="close">
                                <i class="fa fa-check"></i> Perfect
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
