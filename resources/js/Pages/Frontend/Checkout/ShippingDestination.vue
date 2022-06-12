<template>
    <div>
        <div class="content-heading pt-0 mb-0 border-0 font-size-md">
            Alamat Pengiriman
            <div class="float-right">
                <button type="button" class="btn btn-sm btn-secondary" @click="openModal" v-if="value">
                    Pilih Alamat Lain
                </button>
            </div>
        </div>
        <div class="block block-bordered block-rounded block-shadow" v-if="selected">
            <div class="block-header border-3x border-bottom p-10">
                <h3 class="block-title">{{ selected.name }}</h3>
            </div>
            <div class="block-content p-10" >
                <div class="content__top-info">
                    <span class="top-info__name">{{ selected.reciever }}</span>
                    <span class="top-info__phone">{{ selected.phone }}</span>
                </div>
                <div class="content__complete-address">
                    {{ selected.address }}
                </div>
                <div class="content__district">{{ selected.area_text }}</div>
            </div>
        </div>
        <div class="block block-bordered block-rounded block-shadow"  v-else>
            <div class="block-content text-center block-content-full">
                <h3 class="font-weight-bold h4">Alamat Pengiriman Belum Ditambahkan</h3>
                <button type="button" class="btn btn-primary" @click="create()">
                    <i class="si si-plus mr-1"></i>Tambah Alamat
                </button>
            </div>
        </div>

        <b-modal id="addressList" ref="addressList" size="md" content-class="rounded" centered body-class="p-0" no-close-on-backdrop hide-footer hide-header>
            <div class="block block-rounded block-transparent mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Pilih Alamat Pengiriman</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" @click="$bvModal.hide('addressList')">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content font-size-sm">
                    <div class="block">
                        <div class="block block-rounded block-bordered mb-15" v-for="d in dataList" :key="d.id">
                            <div class="block-content">
                                <label class="css-control css-control-primary css-radio payment-wrap d-flex">
                                    <div class="d-flex my-auto">
                                        <input type="radio" class="css-control-input" name="addressSelected" :value="d" v-model="selected">
                                        <span class="css-control-indicator"></span>
                                    </div>
                                    <div class="px-10">
                                        <div class="font-size-md">
                                            <span class="font-w700">{{ d.reciever }} </span>({{ d.name }})
                                        </div>
                                        <div>
                                            {{ d.phone }}
                                        </div>
                                        <div class="content__complete-address">
                                            {{ d.address }}
                                        </div>
                                        <div class="content__district">{{ d.area_text }}</div>
                                    </div>
                                </label>
                            </div>
                            <div class="block-content block-content-full block-content-sm font-size-sm pl-50">
                                <a href="#" @click.prevent="edit(d)" class="font-w700">
                                    Ubah Alamat
                                </a>
                                <a href="#" @click.prevent="setPrimary(d.id)" class="font-w700 ml-20" v-if="d.is_primary != 1">
                                    Jadikan Alamat Utama
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
        
        <b-modal  id="modalCreateAddress" ref="modalCreateAddress" size="lg" rounded centered body-class="p-0" hide-footer hide-header>
            <div class="block block-rounded  block-transparent mb-0">
                <form @submit.prevent="submit">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">{{ title }}</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" @click="closeForm">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content font-size-sm">
                        <div class="form-group">
                            <label for="field-name">Label Alamat</label>
                            <input type="text" :class="{'form-control':true, 'is-invalid' : errors.name}"
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
                                    <v-select :filterable="false"
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
                        <button type="button" class="btn btn-danger" @click.prevent="closeForm">
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
</template>
<script>
import vSelect from 'vue-select';
import _ from 'lodash';
import axios from 'axios';
export default {
    name : 'ShippingDestination',
    components: {
        vSelect
    },
    props : ['value'],
    data(){
        return {
            title : 'Tambah Alamat Baru',
            form : {
                id : null,
                name : null,
                reciever : null,
                phone : null,
                lat : null,
                lng : null,
                address : null,
                postal_code : null,
                area_id : null,
                area_text : null,
                axios : true,
            },
            dataList : [],
            errors : {},
            terms : null,
            modalShow: false,
            editMode: false,
            selected : this.value,
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
        selected(val){
            this.$emit('done', val);
            this.$bvModal.hide('addressList');
        }
    },
    methods :{
        openModal(){
            this.fetchData();
            if(this.$root.window.mobile){
                
            }else{
                this.$bvModal.show('addressList');
            }
        },
        async fetchData(){
            const self = this;
            await axios.get(this.route("user.address.data"),{
                params: {
                    search : this.terms
                }
            })
            .then(function (response) {
                var resp = response.data;
                self.dataList = resp.data;
            })
            .catch(function (error) {
                
            })
        },
        create(){
            if(this.$root.window.mobile){
                
            }else{
                this.$bvModal.show('modalCreateAddress');
            }
        },
        closeForm(){
            this.reset();
            if(this.$root.window.mobile){
                
            }else{
                this.$bvModal.hide('modalCreateAddress');
            }
        },
        reset(){
            this.title = "Tambah Alamat Baru";
            this.form.id = null;
            this.form.name = null;
            this.form.reciever = null;
            this.form.phone = null;
            this.form.address = null;
            this.form.postal_code = null;
            this.form.area_id = null;
            this.form.area_text = null;
            this.form.lat = null;
            this.form.lng = null;

            this.errors = {};
            this.locationSelected = {};
            this.editMode = false;
        },
        onSearch(search, loading) {
            if(search.length) {
                loading(true);
                this.search(loading, search, this);
            }
        },
        submit: function () {
            let data = {};
            data = Object.assign(data, this.form)
            let url = this.editMode ? this.route("user.address.update") : this.route(
                "user.address.store");
            axios.post(url, data)
            .then((res) => {
                if(res.data.fail){
                    this.errors = res.data.errors;
                    this.$swal.close();
                }else{
                    this.$swal.close();
                    this.$bvModal.hide('modalCreateAddress');
                    this.selected = res.data.data;
                    this.$emit('done', res.data.data);
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        showConfirmButton: false,
                        showCancelButton: false,
                        html: `Alamat Baru Berhasil Ditambahkan!`,
                        timer: 1500
                    });
                }
            })
            .catch(function (error) {
                if (error.response) {
                    this.errors = error.response.data.errors;
                }
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
            
            this.$bvModal.hide('addressList');
            if(this.$root.window.mobile){
                
            }else{
                this.$bvModal.show('modalCreateAddress');
            }
        },
    }
}
</script>
