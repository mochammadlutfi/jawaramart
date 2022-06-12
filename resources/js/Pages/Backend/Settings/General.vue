<template>
    <BaseLayout>
        <div class="content">
            <h2 class="content-heading">Settings</h2>
            <div class="block block-rounded block-shadow block-bordered">
                <form @submit.prevent="submit">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">1. General</h3>
                        <div class="block-options">
                            <button type="submit" class="btn btn-alt-primary btn-sm">
                                <i class="si si-paper-plane mr-3"></i>Simpan
                            </button>
                        </div>
                    </div>
                    <div class="block-content block-content-full">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="setting-app_name">App Name</label>
                            <div class="col-lg-7">
                                <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.app_name}" id="setting_app_name" v-model="form.app_name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="setting-app_description">App Name</label>
                            <div class="col-lg-7">
                                <textarea id="setting-app_description" rows="4" v-bind:class="{'form-control':true, 'is-invalid' : errors.app_description}" v-model="form.app_description"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="setting-app_description">App Logo</label>
                            <div class="col-lg-7">
                                <ImageUpload 
                                    :image="(form.app_logo) ? asset(form.app_logo) : null" 
                                    :ratio="1.91/1"
                                    :height="80" 
                                    :width="150" 
                                    @done="(image) => form.app_logo = image"
                                    @removeImage="(image) => form.app_logo = null" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import ImageUpload from '@/components/SingleImageUpload.vue';

export default {
    props: {
        settings: Object,
        errors: Object,
    },
    components: {
        BaseLayout,
        ImageUpload,
    },
    data() {
        return {
            form: this.$inertia.form({
                app_name: this.settings.app_name,
                app_description: this.settings.app_description,
                app_logo : this.settings.app_logo
            }),
            loading : false,
        }
    },
}
</script>
