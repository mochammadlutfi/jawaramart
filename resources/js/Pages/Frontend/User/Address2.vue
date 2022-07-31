<template>
    <UserLayout>
        <div>
            <template v-if="!$root.window.mobile">
                <div class="content-heading pt-3 mb-3">
                    Buku Alamat
                    <div class="float-right">
                        <button type="button" class="btn btn-primary btn-sm" @click.prevent="create">
                            <i class="si si-plus"></i> Tambah Alamat
                        </button>
                    </div>
                </div>
            </template>
            <template v-if="dataList.length">
                <div class="block block-rounded block-shadow block-bordered mb-5" v-for="d in dataList" :key="d.id">
                    <div class="block-header border-3x border-bottom p-10">
                        <h3 class="block-title">{{ d.name }}</h3>
                        <span class="badge badge-primary p-2" v-if="d.is_primary == 1">Alamat Utama</span>
                    </div>
                    <div class="block-content p-10">
                        <div class="content__top-info">
                            <span class="top-info__name">{{ d.reciever }}</span>
                            <span class="top-info__phone">{{ d.phone }}</span>
                        </div>
                        <div class="content__complete-address">
                            {{ d.address }}
                        </div>
                        <div class="content__district">{{ d.area_text }}</div>
                    </div>
                    <div class="block-content block-content-full block-content-sm bg-body-light font-size-sm">
                        <button type="button" @click.prevent="edit(d)" class="btn btn-sm btn-primary">
                            Ubah
                        </button>
                        <button type="button" @click.prevent="destroy(d.id)" class="btn btn-sm btn-danger" :disabled="d.is_primary == 1">
                            Hapus
                        </button>
                        <button type="button" @click.prevent="setMainAddress(d.id)" class="btn btn-sm btn-primary" v-if="d.is_primary == 0">
                            Jadikan Alamat Utama
                        </button>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="text-center">
                    <img class="img-fluid" :src="asset('images/not_found.png')">
                    <h3 class="h4 my-10">Data Tidak Ditemukan</h3>
                </div>
            </template>
            <b-modal v-model="modalShow" size="lg" rounded body-class="p-0" hide-footer hide-header>
                <div class="block block-rounded  block-transparent mb-0">
                    <form @submit.prevent="submit">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">{{ title }}</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" @click="reset">
                                    <i class="fa fa-fw fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content font-size-sm">
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
                                <label for="field-name">Alamat Lengkap</label>
                                <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.address}"
                                    id="field-name" v-model="form.address">
                                <div v-if="errors.address" class="invalid-feedback font-size-sm">{{ errors.address[0] }}</div>
                            </div>
                        </div>
                        <div class="block-content block-content-full text-right border-top p-10">
                            <button type="button" class="btn btn-danger" @click.prevent="reset">
                                <i class="si si-close mr-1"></i>
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="si si-check mr-1"></i>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </b-modal>
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
