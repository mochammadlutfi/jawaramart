<template>
    <div>
        <div class="image-upload-box" :style="{'height': height+'px', 'width' : width+'px'}">
            <div v-if="cropImg" class="img-overlay position-relative">
                <img class="img-fluid" :src="cropImg" />
                <button type="button" class="btn-block-option" @click.prevent="removeImage(id)">
                    <span tabindex="-1" class="btn__content">
                        <svg viewBox="0 0 12.81 12.81" xmlns="http://www.w3.org/2000/svg" width="10" height="10">
                            <g fill="none" stroke="#fff" stroke-linecap="round"
                                stroke-miterlimit="10" stroke-width="1.5">
                                <path d="m.75.75 5.66 5.66 5.65-5.66"></path>
                                <path d="m12.06 12.06-5.65-5.65-5.66 5.65"></path>
                            </g>
                        </svg>
                    </span>
                </button>
            </div>
            <div v-else class="upload-wrap">
                <input type="file" name="image" accept="image/*" @change="setImage">
                <i class="si si-camera mr-1"></i>Pilih Foto
            </div>
        </div>
        
        <b-modal v-model="modalOpen" size="md" content-class="rounded" body-class="p-0" no-close-on-backdrop hide-footer hide-header>
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header">
                    <h3 class="block-title">Cropper</h3>
                    <div class="block-options">
                        <button type="button" @click.prevent="close" class="btn-block-option" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <section class="cropper-area">
                        <div class="img-cropper">
                            <vue-cropper ref="cropper" :aspect-ratio="ratio" :src="imgSrc"
                            :cropBoxResizable="true"/>
                        </div>
                    </section>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-alt-secondary"  @click.prevent="close">
                     <i class="si si-close"></i>
                    Close
                </button>
                <button type="button" class="btn btn-primary btn-noborder" @click.prevent="submitImage">
                    <i class="si si-check"></i> save
                </button>
            </div>
        </b-modal>
    </div>
</template>
<script>

import VueCropper from "vue-cropperjs";
import "cropperjs/dist/cropper.css";
export default {
    name: 'ProductFormImage',
    components: {
        VueCropper,
    },
    props: ['data', 'height', 'width', 'imageWidth', 'imageHeight', 'ratio'],
    data() {
        return {
            id: null,
            imgSrc: null,
            cropImg: null,
            filename: "",
            mimeType: "",
            modalOpen: false,
            heightWrap : null,
        };
    },
    watch: {
        data(value) {
            this.id = value.id;
            this.cropImg = value.image;
            if(value){
                this.heightWrap = '100%';
            }else{
                this.heightWrap = this.height+'px';
            }
        },
        cropImg(value){
            if(value){
                this.heightWrap = '100%';
            }else{
                this.heightWrap = this.height+'px';
            }
        }
    },
    methods: {
        getHeightWrap(){
            if(this.cropImg){
                this.heightWrap = '100%';
            }else{
                this.heightWrap = this.height+'px';
            }
        },
        setImage(e) {
            const file = e.target.files[0];

            if (!file.type.includes('image/')) {
                alert('Please select an image file');
                return;
            }
            if (typeof FileReader === 'function') {
                const reader = new FileReader();

                reader.onload = (event) => {
                    this.imgSrc = event.target.result;
                    this.$refs.cropper.replace(event.target.result);
                };

                reader.readAsDataURL(file);
            } else {
                alert('Sorry, FileReader API not supported');
            }
            
            this.filename = file.name
            this.mimeType = file.type
            this.modalOpen = true;
            this.getHeightWrap();
        },
        cropImage() {
            this.cropImg = this.$refs.cropper.getCroppedCanvas({
                minWidth: this.imageWidth,
                minHeight: this.imageHeight,
                fillColor: '#fff',
                imageSmoothingEnabled: false,
                imageSmoothingQuality: 'high',
            }).toDataURL();
            this.getHeightWrap();
        },
        async dataURLToFile(imageString, filename, mimeType) {

            const res = await fetch(imageString);
            const blob = await res.blob();
            return new File([blob], filename, {
                type: mimeType
            });
        },
        async submitImage() {
            await this.cropImage();
            const imageFileResponse = await this.dataURLToFile(this.cropImg, this.filename, this.mimeType)
            let data = {
                id : this.id,
                image : imageFileResponse,
            }
            this.$emit('done', data)
            this.modalOpen = false;

        },
        removeImage(id){
            this.getHeightWrap();
            this.cropImg = null;
            this.$emit('removeImage', this.id);
        },
        close(){
            this.id = null;
            this.cropImg = null;
            this.modalOpen = false;
            this.imimgSrc = null;
            this.$emit('close');
        }
    },
}
</script>

<style>
.image-upload-box {
    box-sizing: border-box;
    border-radius: 8px;
    margin-top: 10px;
    margin-bottom: 10px;
    cursor: pointer;
    border: 2px dashed #DBDEE2;
}

.image-upload-box .upload-wrap input {
    width: 100%;
    padding: 10px 0px;
    position: absolute;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.image-upload-box .img-overlay img {
    width: 100%;
    background-color: #FFFFFF;
    border-radius: 8px;
    border-image: initial;
    background-repeat: no-repeat;
    background-position: center center;
    object-fit: contain;
}

.image-upload-box .img-overlay button {
    top: -12px;
    right: -10px;
    position: absolute;
    display: flex;
    text-align: center;
    color: white;
    vertical-align: middle;
    cursor: pointer;
    user-select: none;
    background-color: #E44C65;
    border: 1px solid transparent;
    padding: 0;
    font-size: 16px;
    line-height: 1.5;
    border-radius: 100px;
    box-shadow: 0 0 1px #949494;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}


.image-upload-box .img-overlay button span {
    display: flex;
    padding : 6px;
}

.image-upload-box .upload-wrap {
    display: flex;
    flex-direction: column;
    -webkit-box-pack: center;
    place-content: center;
    width: inherit;
    height: inherit;
    -webkit-box-align: center;
    align-items: center;
}
</style>