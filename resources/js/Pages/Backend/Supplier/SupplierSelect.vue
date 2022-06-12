<template>
    <div>
        <v-select label="nama" :filterable="false" :options="options" @search="onSearch" v-bind:class="{'form-control':true, 'p-0' : true, 'is-invalid' : error }" v-model="selected">
            <template slot="no-options">
                <div class="p-5 text-left">
                    Cari Supplier
                </div>
            </template>
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
        <div class="invalid-feedback" v-if="error">{{ error[0] }}</div>
    </div>
</template>
<script>

import vSelect from 'vue-select';
import _ from 'lodash';
export default {
    name : 'SupplierSelect',
    components: {
        vSelect
    },
    data(){
        return {
            options : [],
            selected : null,
        }
    },
    watch : {
        selected(value) {
            if(value){
                this.$emit('done', value.anggota_id)
            }else{
                this.$emit('done', null)
            }
        },
    },
    mounted (){
        this.search();
    },
    props : ['error'],
    methods :{
        onSearch(search, loading) {
            if(search.length) {
                loading(true);
                this.search(loading, search, this);
            }
        },
        search: _.debounce((loading, search, vm) => {
            fetch(vm.route('admin.purchase.supplier.data') +`?q=${escape(search)}`).then(res => {
                res.json().then(json => (vm.options = json));
                loading(false);
            });
        }, 350),
    },
    setup() {
        
    },
}
</script>