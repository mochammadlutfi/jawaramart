<template>
    <BaseLayout :title="title">
        <div class="content">
            <form @submit.prevent="submit">
                <div class="content-heading pt-0 mb-3">
                    {{ title }}
                    <div class="float-right">
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="si si-paper-plane mr-1"></i>
                            Save
                        </button>
                    </div>
                </div>
                <div class="block block-bordered block-shadow block-rounded">
                    <div class="block-content">
                        <div class="row">
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="field-avatar">Full Name</label>
                                    <div class="push">
                                        <img :src="(previewImg) ? previewImg : asset('images/avatar.jpg') " class="img-fluid">
                                    </div>
                                        <b-form-file
                                        accept="image/jpeg, image/jpg, image/png, image/gif"
                                        @change="setImage"
                                        placeholder="Choose a file or drop it here..."
                                        drop-placeholder="Drop file here..."
                                        ></b-form-file>
                                    <!-- <div class="custom-file">

                                        <input type="file" class="custom-file-input" name="image" accept="image/*" @change="setImage">
                                        <label class="custom-file-label" for="profile-settings-avatar">Choose new avatar</label>
                                    </div> -->
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="field-name">Full Name</label>
                                    <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.name}" id="field-name" v-model="form.name" >
                                    <div v-if="errors.name" class="invalid-feedback">
                                        {{ errors.name[0] }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="field-username">Username</label>
                                    <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.username}" id="field-username" v-model="form.username" >
                                   <div v-if="errors.username" class="invalid-feedback">
                                        {{ errors.username[0] }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="field-email">Email</label>
                                    <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.email}" id="field-email" v-model="form.email" >
                                    <div v-if="errors.email" class="invalid-feedback">
                                        {{ errors.email[0] }}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="field-role">Role</label>
                                    <v-select label="name" :filterable="false" :reduce="(option) => option.id" :options="roles" :class="{'is-invalid' : errors}" v-model="form.role">
                                        <template slot="option" slot-scope="option">
                                            <div class="d-center">
                                                {{ option.name }}
                                                </div>
                                            </template>
                                            <template slot="selected-option" slot-scope="option">
                                            <div class="selected d-center">
                                                {{ option.name }}
                                            </div>
                                        </template>
                                    </v-select>
                                    <div v-if="errors.role" class="invalid-feedback">
                                        {{ errors.role[0] }}
                                    </div>
                                </div>
                                <template v-if="!editMode">
                                    <div class="form-group">
                                        <label for="field-password">Password</label>
                                        <PasswordInput v-model="form.password" :error="errors.password" id="field-password"/>
                                        <div v-if="errors.password" class="text-danger text-sm">
                                            {{ errors.password[0] }}
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="field-password_confirm">Password Confirmation</label>
                                        <PasswordInput v-model="form.password_confirm" :error="errors.password_confirm" id="field-password_confirm"/>
                                        <div v-if="errors.password_confirm" class="text-danger text-sm">
                                            {{ errors.password_confirm[0] }}
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div v-show="modalOpen">
            <transition name="modal-fade">
                <div class="modal-backdrop">
                    <div class="modal" tabindex="-1" aria-labelledby="modal-large" style="display: block; padding-right: 17px;" aria-modal="true" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="block block-themed block-transparent mb-0">
                                    <div class="block-header bg-primary-dark">
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
                                                <vue-cropper ref="cropper" :src="imgSrc" :aspect-ratio="1/1" :cropBoxResizable="true"/>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-alt-secondary"  @click.prevent="close">Close</button>
                                    <button type="button" class="btn btn-primary btn-noborder" @click.prevent="submitImage">
                                        <i class="fa fa-check"></i> save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </BaseLayout>
</template>
<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import PasswordInput from '@/Components/Form/PasswordInput';
import vSelect from 'vue-select';

import VueCropper from "vue-cropperjs";
import "cropperjs/dist/cropper.css";
export default {
    components: {
        BaseLayout,
        PasswordInput,
        VueCropper,
        vSelect
    },
    props : {
        roles : Array,
        data : Object,
        errors : Object,
        editMode : Boolean
    },
    data(){
        return {
            form : {
                id : null,
                name : null,
                email : null,
                avatar : null,
                role : null,
                password : null,
                password_confirm : null,
            },
            title : "Add New Staff",
            imgSrc: null,
            previewImg : null,
            cropImg: this.image,
            filename: "",
            mimeType: "",
            modalOpen: false,
            heightWrap : null,
        }
    },
    mounted(){
        if(this.editMode){
            this.editModeActive();
        }
    },
    methods : {
        getHeightWrap(){
            if(this.form.avatar){
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
            this.previewImg = this.$refs.cropper.getCroppedCanvas({
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
            const imageFileResponse = await this.dataURLToFile(this.previewImg, this.filename, this.mimeType)
            this.form.avatar = imageFileResponse;
            this.modalOpen = false;

        },
        removeImage(){
            this.getHeightWrap();
            this.form.avatar = null;
            this.previewImg = null;
        },
        close(){
            this.form.avatar = null;
            this.modalOpen = false;
            this.imimgSrc = null;
            this.$emit('close');
        },
        submit: function () {
            this.$swal.fire({
                title: 'Please wait...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            let form = this.$inertia.form(this.form);
            let url = this.editMode ? this.route("admin.settings.staff.update") : this.route(
                "admin.settings.staff.store");
            form.post(url, {
                preserveScroll: true,
                onSuccess: () => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Data saved successfully!`,
                        showConfirmButton: false,
                    });
                    this.reset();
                },
                onError:(error) => {
                    this.$swal.close();
                },
            });
        },
        editModeActive(){
            this.title = 'Edit Staff';
            this.form.id = this.data.id;
            this.form.name = this.data.name;
            this.form.username = this.data.username;
            this.form.email = this.data.email;
            this.form.role = this.data.roles[0].id;
            this.previewImg = this.data.avatar_url;
        }
    }
}
</script>
