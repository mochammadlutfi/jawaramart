<template>
    <UserLayout title="Tambah Alamat">
        <div>
            <template v-if="!$root.window.mobile">
                <div class="content-heading pt-3 mb-3">
                    Tambah Alamat
                </div>
            </template>
            <form @submit.prevent="submit">
                <div class="form-group">
                    <label for="field-name">Label Alamat</label>
                    <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.name}"
                        id="field-name" v-model="form.name" placeholder="Rumah, Kantor, dll">
                    <div v-if="errors.name" class="invalid-feedback font-size-sm">{{ errors.name[0] }}</div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="field-reciever">Nama Penerima</label>
                            <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.reciever}"
                                id="field-reciever" v-model="form.reciever">
                            <div v-if="errors.reciever" class="invalid-feedback font-size-sm">{{ errors.reciever[0] }}</div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="field-phone">No Handphone</label>
                            <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.phone}"
                                id="field-phone" v-model="form.phone">
                            <div v-if="errors.phone" class="invalid-feedback font-size-sm">{{ errors.phone[0] }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="form-group">
                            <label for="field-name">Kelurahan / Kecamatan</label>
                            <v-select label="area_text"  :filterable="false"
                            :options="locations"
                            v-model="locationSelected"
                            v-bind:class="{'is-invalid' : errors.area_id }" 
                            @search="onSearch">
                            <template slot="no-options">
                                <div class="p-5 text-left">
                                    Masukan nama kecamatan atau kelurahan
                                </div>
                            </template>
                                <template slot="option" slot-scope="option">
                                    <div class="d-center">
                                        {{ option.area_text}}
                                        </div>
                                    </template>
                                    <template slot="selected-option" slot-scope="option">
                                    <div class="selected d-center">
                                        {{ option.area_text}}
                                    </div>
                                </template>
                                <!-- <template #list-footer>
                                    <li ref="load" class="loader" v-show="hasNextPage">Loading more options...</li>
                                </template> -->
                            </v-select>
                            <div v-if="errors.area_id" class="text-danger font-size-sm">{{ errors.area_id[0] }}</div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="field-postal_code">Kode POS</label>
                            <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.postal_code}"
                                id="field-postal_code" v-model="form.postal_code" :disabled="!locationSelected">
                            <div v-if="errors.postal_code" class="invalid-feedback font-size-sm">{{ errors.postal_code[0] }}</div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="field-address">Alamat Lengkap</label>
                    <textarea class="form-control" :class="{'is-invalid' : errors.address}" id="field-address" v-model="form.address"></textarea>
                    <div v-if="errors.address" class="invalid-feedback font-size-sm">{{ errors.address[0] }}</div>
                </div>
                <div class="row justify-content-end">
                    <div class="col-lg-3">
                        <button type="submit" class="btn btn-primary btn-block">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </UserLayout>
</template>
<style>
.vs__selected .selected {
    max-width: 420px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
<script>
import UserLayout from "@/Layouts/Frontend/UserLayout";
import vSelect from 'vue-select';
import _ from 'lodash';
import axios from 'axios';
export default {
    components: {
        UserLayout,
        vSelect
    },
    props : ['dataList', 'errors'],
    data(){
        return {
            title : 'Tambah Alamat Baru',
            form : {
                name : null,
                reciever : null,
                phone : null,
                lat : null,
                lng : null,
                address : null,
                postal_code : null,
                area_id : null,
                area_text : null
            },
            modalShow: false,
            editMode: false,
            locations : [],
            locationSelected : null,
        }
    },
    watch :{
        locationSelected(val){
            if(val){
                this.form.lat = val.lat;
                this.form.lng = val.lng;
                this.form.postal_code = val.postal_code;
                this.form.area_id = val.area_id;
                this.form.area_text = val.area_text;
            }else{
                this.form.postal_code = null;
                this.form.lat = null;
                this.form.lng = null;
                this.form.area_id = null;
                this.form.area_text = null;
            }
        },
    },
    methods :{
        create(){
            this.modalShow = true;
        },
        reset(){
            this.title = "Tambah Alamat Baru";
            this.modalShow = false;
            this.editMode = false;
        },
        onSearch(search, loading) {
            if(search.length) {
                loading(true);
                this.search(loading, search, this);
            }
        },
        submit: function () {
            let form = this.$inertia.form(this.form)
            let url = this.editMode ? this.route("user.address.update") : this.route(
                "user.address.store");
            form.post(url, {
                preserveScroll: true,
                onProgress: ()=> {
                    this.$swal.fire({
                        title: 'Tunggu Sebentar...',
                        text: '',
                        imageUrl: window._asset + 'media/loading.gif',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    });
                },
                onSuccess: () => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Alamat Baru Berhasil Ditambahkan!`,
                        showConfirmButton : false,
                    });
                    this.reset();
                },
            });
        },
        search: _.debounce((loading, search, vm) => {

            let params = {
                keyword : search
            };

            axios.get(vm.route('shipper.sub_urban'), {
                params,
            })
            .then(function (response) {
                const resp = response.data;
                vm.locations = [];
                resp.data.forEach(val => {
                    vm.locations.push({
                        area_id : val.adm_level_5.id,
                        area_text : val.adm_level_2.name +', '+ val.adm_level_3.name +', '+ val.adm_level_4.name + ', ' + val.adm_level_5.name,
                        postal_code : val.postcodes[0],
                        lat : val.adm_level_5.geo_coord.lat,
                        lng : val.adm_level_5.geo_coord.lng,
                    });
                });
                loading(false);
            })
            .catch(function (error) {
                
            })
        }, 350),
        edit(data){
            this.reset();

            this.title = "Ubah Alamat";
            this.modalShow = true;
            this.editMode = true;

            this.form.id = data.id;
            this.form.name = data.name;
            this.form.reciever = data.reciever;
            this.form.phone = data.phone;
            this.form.address = data.address;
            this.form.postal_code = data.postal_code;
            this.form.area_id = data.area_id;
            this.form.area_text = data.area_text;
            this.form.lat = data.lat;
            this.form.lng = data.lng;

            this.locationSelected = {
                area_id : data.area_id,
                area_text : data.area_text,
                postal_code : data.postal_code,
                lat : data.lat,
                lng : data.lng,
            }
        },
        setMainAddress(value){
            let form = this.$inertia.form({id : value})
            form.post(this.route("user.address.main"), {
                preserveScroll: true,
                onProgress: ()=> {
                    this.$swal.fire({
                        title: 'Tunggu Sebentar...',
                        text: '',
                        imageUrl: window._asset + 'media/loading.gif',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    });
                },
                onSuccess: () => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: `Alamat Utama Berhasil Diperbaharui!`,
                        showConfirmButton: false,
                    });
                },
            });
        },
        destroy: function (id) {
            this.$swal.fire({
                icon: 'warning',
                title: 'Kamu Yakin?',
                text: `Alamat yang dihapus tidak bisa dikembalikan!`,
                showCancelButton: true,
                cancelButtonText : 'Tidak, Batal',
                confirmButtonText: 'Ya, Hapus',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(this.route('user.address.delete', id), {
                        preserveScroll: true
                    })
                }
            })
        },

    }
 }

</script>
