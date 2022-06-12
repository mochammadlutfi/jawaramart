<template>
    <div>
        <v-select label="name"
         :options="options"
         v-bind:class="{'is-invalid' : error }" 
         v-model="selected">
            <template slot="no-options">
                <div class="p-5 text-left">
                    Cari Customer
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
import axios from 'axios';
export default {
    name : 'CustomerSelect',
    components: {
        vSelect
    },
    data(){
        return {
            options : [],
            selected : null,
            search : null,
        }
    },
    watch : {
        selected(value) {
            if(value){
                this.$emit('done', value.id)
            }else{
                this.$emit('done', null)
            }
        },
    },
    created(){
        this.getSelection();
        if(this.data){
            this.selected = this.data;
        }
    },
    props : ['error', 'data', 'default'],
    methods :{
        onSearch(search, loading) {
            if(search.length) {
                this.search = search;
                this.getSelection();
            }
        },
        async getSelection(){
            let params = {};

            if(this.search){
                let search = {
                    search : this.search
                }
                params = Object.assign(params, search)
            }

            const self = this;
            await axios.get(this.route("admin.customer.data"), params)
            .then(function (response) {
                const resp = response.data;
                self.options = response.data.map((item) => {
                    return {
                        id: item.id,
                        name: (item.anggota_id)? item.anggota_id + ' - ' + item.name : item.name,
                    };
                });
                if(self.default){
                    self.selected = self.options[0];
                }
            })
            .catch(function (error) {
                
            })
        },
    },
    setup() {
        
    },
}
</script>