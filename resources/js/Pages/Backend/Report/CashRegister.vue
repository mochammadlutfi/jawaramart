<template>
    <BaseLayout title="Admin | Cash Register Report">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Cash Register Report
                <div class="float-right">
                    <button v-if="selected.length > 0" type="button" class="btn btn-sm btn-danger">
                        <i class="si si-trash"></i>
                        Delete {{ selected.length }} Selected
                    </button>
                    <a class="btn btn-sm btn-secondary" :href="route('admin.report.cash_register', { 'excel' : 1 } )" v-if="hasPermission('Product', 'create')">
                        <i class="si si-plus"></i>
                        Download Excel
                    </a>
                </div>
            </div>
            <div class="block block-rounded block-shadow block-bordered d-md-block d-none mb-10">
                <div class="block-content p-2">
                    <div class="row justify-content-between">
                        <div class="col-4">
                            <div class="has-search">
                                <i class="fa fa-search"></i>
                                <input type="search" class="form-control" id="search-data-list" v-model="search" @keyup="doSearch()" @change="doSearch()">
                            </div>
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4">
                            <div class="d-flex float-right">
                                <div class="my-auto px-3">
                                    <span>{{ dataList.from }}-{{ dataList.to }}/{{ dataList.total }}</span>
                                </div>
                                <div class="pt-25 pl-0">
                                    <button @click="prevPage" class="btn btn-alt-secondary mx-1" type="button"
                                    :disabled="checkPaginate('prev')">
                                        <i class="fa fa-chevron-left fa-fw"></i>
                                    </button>
                                    <button @click="nextPage" class="btn btn-alt-secondary mx-1" type="button"
                                    :disabled="checkPaginate('next')">
                                        <i class="fa fa-chevron-right fa-fw"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block block-rounded block-shadow-2 block-bordered mb-5">
                <div class="block-content px-0 py-0">
                    <table class="table table-striped table-hover table-vcenter mb-0 table-sm">
                        <thead class="thead-light text-center">
                            <tr>
                                <th @click="sort('opened_at')">Open Time</th>
                                <th @click="sort('purchase_price')" width="15%">Close Time</th>
                                <th @click="sort('stock_count')">Staff</th>
                                <th @click="sort('stock_value_sp')">Open Amount</th>
                                <th @click="sort('stock_value_pp')">Close Amount</th>
                                <th @click="sort('stock_value_pp')">Revenue</th>
                                <th width="15%"></th>
                            </tr>
                        </thead>
                        <tbody v-if="loading">
                            <tr>
                                <td colspan="5">
                                    <div class="text-center py-50">
                                        <div class="spinner-border text-primary wh-50" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <template v-if="Object.values(dataList.data).length">
                                <tr v-for="data in dataList.data" :key="data.id">
                                    <td>{{ format_date(data.opened_at) }}</td>
                                    <td>{{ format_date(data.closed_at) }}</td>
                                    <td>
                                        <div class="font-w500">{{ data.staff.name }}</div>
                                        <div class="font-w700">{{ data.staff.email }}</div>
                                    </td>
                                    <td>{{ currency(data.opened_amount) }}</td>
                                    <td>{{ currency(data.closed_amount) }}</td>
                                    <td>{{ currency(data.revenue) }}</td>
                                    <td>

                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-if="!Object.values(dataList.data).length">
                                    <td>Data Kosong</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import moment from 'moment';
import _ from 'lodash';

export default {
    components: {
        BaseLayout,
        Link,
    },
    data(){
        return {
            loading : false,
            selected: [],
            selectAll: false,
            search : this.route().params.search == undefined ? '' : this.route().params.search,
            currentSort: this.route().params.sort == undefined ? 'name' : this.route().params.sort,
            currentSortDir: this.route().params.sortDir == undefined ? 'asc' : this.route().params.sortDir,
            currentPage: this.route().params.page == undefined ? 1 : this.route().params.page,
        } 
    },
    watch: {
        search: function () {
            this.doSearch() 
        },
        selectAll: function(val){
			this.selected = [];
            if(val){
                this.dataList.data.forEach((value, index) => {
                    this.selected.push(value.id)
                });
            }
        }
    },
    props: {
        dataList: Object,
    },
    methods :{
        sort:function(s) {

            if(s === this.currentSort) {
                this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' :'asc';
            }

            this.currentSort = s;

            let params = {
                search : this.search,
                page : 1,
                sort : this.currentSort,
                sortDir : this.currentSortDir
            }
            
            this.$inertia.get(this.route('admin.report.cash_register', params), {
                preserveScroll : true,
            });
        },
        checkPaginate(type){
            const vm = this;
            if(vm.dataList){
                if(type == 'next'){
                    return (vm.dataList.next_page_url) ? false : true;
                }else{
                    return (vm.dataList.prev_page_url) ? false : true;
                }
            }else{
                return true;
            }
        },
        nextPage: function() {
            if(this.currentPage < this.dataList.total){
                this.currentPage++;

                let params = {
                    search : this.search,
                    page : this.currentPage,
                    sort : this.currentSort,
                    sortDir : this.currentSortDir
                }
                
                this.$inertia.get(this.route('admin.report.cash_register', params), {
                    preserveScroll : true,
                });
            }
        },
        prevPage: function() {
            if(this.currentPage > 1){
                this.currentPage--;

                 let params = {
                    search : this.search,
                    page : this.currentPage,
                    sort : this.currentSort,
                    sortDir : this.currentSortDir
                }
                
                this.$inertia.get(this.route('admin.report.cash_register', params), {
                    preserveScroll : true,
                });
            }
        },
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MMMM YYYY hh:mm')
            }
        },
        doSearch : _.throttle(function(){

            let params = {
                search : this.search,
                page : 1,
                sort : this.currentSort,
                sortDir : this.currentSortDir
            }
            this.$inertia.get(this.route('admin.report.cash_register', params), {
                preserveScroll : true,
            })
        }, 200),
    }
}
</script>
