<template>
    <BaseLayout title="Product List">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Products
                <div class="float-right">
                    <button v-if="selected.length > 0" type="button" class="btn btn-sm btn-danger">
                        <i class="si si-trash"></i>
                        Delete {{ selected.length }} Selected
                    </button>
                    <a class="btn btn-sm btn-secondary" :href="route('admin.product.create')" v-if="hasPermission('Product', 'create')">
                        <i class="si si-plus"></i>
                        Create
                    </a>
                </div>
            </div>
            <div class="block block-rounded block-shadow block-bordered d-md-block d-none mb-10">
                <div class="block-content p-2">
                    <div class="row justify-content-between">
                        <div class="col-4">
                            <div class="has-search">
                                <i class="fa fa-search"></i>
                                <input type="search" class="form-control" id="search-data-list" v-model="search" @input="doSearch" autocomplete="off">
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
                    <table class="table table-striped table-vcenter table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="2%">
                                    <b-form-checkbox id="selectAll" name="selectAll" v-model="selectAll"></b-form-checkbox>
                                </th>
                                <th @click="sort('name')" width="48%">
                                    Product
                                    <span v-if="currentSort == 'name'" v-html="sortIcon" />
                                </th>
                                <th @click="sort('sell_price')" width="15%">
                                    Sell Price
                                    <span v-if="currentSort == 'sell_price'" v-html="sortIcon" />
                                </th>
                                <th @click="sort('purchase_price')" width="16%">
                                    Purchase Price
                                    <span v-if="currentSort == 'purchase_price'" v-html="sortIcon" />
                                </th>
                                <th @click="sort('stock')" width="10%">
                                    Stock
                                    <span v-if="currentSort == 'stock'" v-html="sortIcon" />
                                </th>
                                <th @click="sort('sold')" width="12%">
                                    Sold
                                    <span v-if="currentSort == 'sold'" v-html="sortIcon" />
                                </th>
                                <th width="8%"></th>
                            </tr>
                        </thead>
                        <tbody v-if="loading">
                            <tr>
                                <td colspan="7">
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
                                    <td>
                                        <b-form-checkbox
                                            :id="'data-'+data.id"
                                            v-model="selected"
                                            :name="'data-'+data.id"
                                            :value="data.id"
                                            >
                                        </b-form-checkbox>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <img class="media-object" :src="data.main_image" style="max-width:45px">
                                            <div class="pl-3">
                                                <div class="font-w600">{{ data.name }}</div>
                                                {{ data.category.name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ currency(data.sell_price) }} 
                                        <template v-if="data.variant.length > 1 && data.sell_price != data.sell_max_price">
                                        ~ {{ currency(data.sell_max_price) }}
                                        </template>
                                    </td>
                                    <td>
                                        {{ currency(data.purchase_price) }} 
                                    </td>
                                    <td>{{ (data.stock) ? data.stock : 0 }}</td>
                                    <td>{{ (data.sold) }}</td>
                                    <th>
                                        <b-dropdown text="Action" class="m-md-2"  size="sm">
                                            <a :href="route('admin.product.edit', {id : data.id})" class="dropdown-item">
                                                <i class="si si-note mr-3"></i>
                                                Edit
                                            </a>
                                            <a href="javascript:void(0)" class="dropdown-item" @click="updateStock(data)">
                                                <i class="si si-note mr-3"></i>
                                                Update Stock
                                            </a>
                                        </b-dropdown>
                                    </th>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-if="!Object.values(dataList.data).length">
                                    <td colspan="7">
                                        <div class="text-center">
                                            <img class="img-fluid" :src="asset('images/not_found.png')">
                                            <h3 class="font-size-24 font-w600 mt-3">Data Not Found</h3>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <product-stock-update ref="ProductStockUpdate"/>
    
    </BaseLayout>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import moment from 'moment';
import _ from 'lodash';
import ProductStockUpdate from './UpdateStock.vue';
export default {
    components: {
        BaseLayout,
        Link,
        ProductStockUpdate
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
    computed: {
        sortIcon: function() {
            if (this.currentSortDir === "asc") {
                return '<i class="fa fa-sort-amount-up-alt"></i>';
            } else {
                return '<i class="fa fa-sort-amount-down-alt"></i>';
            }
        },
    },
    methods :{
        sort:function(s) {

            if(s === this.currentSort) {
                this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' :'asc';
            }

            this.loading = true;
            this.currentSort = s;
            this.fetchData();
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
                this.loading = true;
                this.fetchData(this.currentPage);
            }
        },
        prevPage: function() {
            if(this.currentPage > 1){
                this.loading = true;
                this.currentPage--;
                this.fetchData(this.currentPage);
            }
        },
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MMM YYYY')
            }
        },
        doSearch : _.debounce(function(){
            this.loading = true;
            this.fetchData();
        }, 1000),

        fetchData(page = 1){
            let params = {
                search : this.search,
                page : page,
                sort : this.currentSort,
                sortDir : this.currentSortDir
            }

            this.$inertia.get(this.route(this.route().current(), params), {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => {
                    this.loading = false;
                },
            });
        },
        updateStock(product){
            this.$refs.ProductStockUpdate.openModal(product);
        },
    }
}
</script>
