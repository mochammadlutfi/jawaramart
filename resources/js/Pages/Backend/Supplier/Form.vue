<template>
    <BaseLayout title="Dashboard">
        <div class="content">
            <form @submit.prevent="submit">
                <div class="content-heading pt-0 mb-3">
                    Tambah Supplier
                    <div class="float-right">
                        <button type="submit" class="btn btn-secondary mr-5 btn-sm">
                            <i class="si si-paper-plane mr-1"></i>
                            Simpan
                        </button>
                    </div>
                </div>
                <div class="block block-rounded block-shadow">
                    <div class="block-content block-content-full">
                        <div class="row">
                            <div class="col-8">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label" for="field-name">Nama</label>
                                    <div class="col-lg-9">
                                        <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.name}" id="field-name" v-model="form.name" >
                                        <div v-if="errors.name" class="invalid-feedback font-size-sm">{{ errors.name[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label" for="field-email">Email</label>
                                    <div class="col-lg-9">
                                        <input type="email" v-bind:class="{'form-control':true, 'is-invalid' : errors.email}" id="field-email" v-model="form.email" >
                                        <div v-if="errors.email" class="invalid-feedback font-size-sm">{{ errors.email[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label" for="field-no_hp">No Handphone</label>
                                    <div class="col-lg-9">
                                        <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.no_hp}" id="field-no_hp" v-model="form.no_hp" >
                                        <div v-if="errors.no_hp" class="invalid-feedback font-size-sm">{{ errors.no_hp[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label" for="field-no_phone">No Telepon</label>
                                    <div class="col-lg-9">
                                        <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.no_phone}" id="field-no_phone" v-model="form.no_phone" >
                                        <div v-if="errors.no_phone" class="invalid-feedback font-size-sm">{{ errors.no_phone[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label" for="field-region_id">Daerah</label>
                                    <div class="col-lg-9">
                                        <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.region_id}" id="field-region_id" v-model="form.region_id" >
                                        <div v-if="errors.region_id" class="invalid-feedback font-size-sm">{{ errors.region_id[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label" for="field-postal_code">Kode POS</label>
                                    <div class="col-lg-9">
                                        <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.postal_code}" id="field-postal_code" v-model="form.postal_code" >
                                        <div v-if="errors.postal_code" class="invalid-feedback font-size-sm">{{ errors.postal_code[0] }}</div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label" for="field-address">Alamat Lengkap</label>
                                    <div class="col-lg-9">
                                        <textarea v-bind:class="{'form-control':true, 'is-invalid' : errors.address}" id="field-name" v-model="form.address" rows="4"></textarea>
                                        <div v-if="errors.address" class="invalid-feedback font-size-sm">{{ errors.address[0] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import flatPickr from 'vue-flatpickr-component';
import axios from 'axios';
export default {
    components: {
        BaseLayout,
        flatPickr,
    },
    data(){
        return {
            form : {
                id : null,
                name : null,
                address : null,
                no_hp : null,
                email : null,
                no_phone : null,
                postal_code : null,
                teritory_id : null,
            }
        }
    },
    props: {
        errors : Object,
        data: Object,
        editMode: Boolean,
    },
    methods: {
        submit: function(){
            this.$swal.fire({
                title: 'Please Wait...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            let form = this.$inertia.form(this.form)
            let url = this.editMode ? this.route("admin.purchase.supplier.update") : this.route("admin.purchase.supplier.store");
            form.post(url, {
                preserveScroll: true,
                onProgress: () => {
                },
                onSuccess: () => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        html: `Data Has Been Saved Sucessfully!
                        <br><br>
                            <a href="${ this.route('admin.purchase.supplier.store') }" class="btn btn-outline-primary">
                                <i class="si si-plus mr-1"></i>Tambah Lainnya
                            </a> 
                            <a href="${ this.route('admin.purchase.supplier.index') }" class="btn btn-primary">
                                <i class="si si-action-undo mr-1"></i>Kembali
                            </a>`,
                    })
                    this.reset();
                },
                onError:(error) => {
                    this.$swal.close();
                    console.log(error);
                },
            });
        },
    }
}
</script>
