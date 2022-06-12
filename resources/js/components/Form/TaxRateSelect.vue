<template>
    <div>
        <v-select label="name"
         :options="options"
         v-bind:class="{'p-0' : true, 'is-invalid' : error }" 
         v-model="selected">
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
    name : 'SupplierSelect',
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
                this.$emit('done', value)
            }else{
                this.$emit('done', null)
            }
        },
    },
    mounted (){
        this.getSelection();
        if(this.data){
            this.selected = this.data;
        }
    },
    props : ['error', 'data'],
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
            await axios.get(this.route("admin.settings.tax.data"), params)
            .then(function (response) {
                const resp = response.data;
                self.options = resp;
            })
            .catch(function (error) {
                
            })
        },
    },
    setup() {
        
    },
}
</script>