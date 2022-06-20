<template>
    <BaseLayout title="List Sales">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                List Sales Return ({{ dataList.total }})
            </div>
            
            <div class="block block-rounded block-shadow block-bordered d-md-block d-none mb-10">
                <div class="block-content p-2">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="has-search">
                                <i class="fa fa-search"></i>
                                <input type="search" class="form-control" id="search-data-list" v-model="search" @keyup="doSearch()" @change="doSearch()" autofocus>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <v-select v-model="sorted" :options="sorting" placeholder="Urut Berdasarkan"></v-select>
                        </div>
                        <div class="col-lg align-self-end">
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
                                <th>Date</th>
                                <th>Return No</th>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Payment Status</th>
                                <th>Total Amount</th>
                                <th>Total Items</th>
                                <th width="10%"></th>
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
                                    <td>{{ data.date }}</td> 
                                    <td>{{ data.ref }}</td>
                                    <td>{{ data.sale.ref }}</td>
                                    <td>{{ data.sale.customer.name }}</td>
                                    <td>
                                        <span class="badge badge-success" v-if="data.payment_status == 'paid'">Paid</span>
                                        <span class="badge badge-info" v-else-if="data.payment_status == 'partial'">Partial</span>
                                        <span class="badge badge-warning" v-else-if="data.payment_status == 'pending'">Pending</span>
                                        <span class="badge badge-danger" v-else>Unpaid</span>
                                    </td>
                                    <td>{{ currency(data.grand_total) }}</td>
                                    <td>{{ data.line_count }}</td>
                                    <th>
                                        <a :href="route('admin.sale.return.show', {id : data.id})" class="btn btn-secondary btn-sm">
                                            <i class="si si-eye mr-1"></i>
                                            Detail
                                        </a>
                                    </th>
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
import moment from 'moment';
import _ from 'lodash';
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import vSelect from 'vue-select';
import CustomerSelect  from '@/components/Form/CustomerSelect.vue';
export default {
    components: {
        BaseLayout,
        Link,
        vSelect,
        CustomerSelect,
    },
    data(){
        return {
            loading : false,
            selected: [],
            selectAll: false,
            search : this.route().params.search == undefined ? '' : this.route().params.search,
            currentPage: this.route().params.page == undefined ? 1 : this.route().params.page,
            sorted : this.route().params.sorted == undefined ? null : this.route().params.sorted,
            sorting : [
                { label : 'Terbaru', value : 'latest'},
                { label : 'Terlama', value : 'oldest'},
            ],
        } 
    },
    props: {
        dataList: Object,
        overview : Array
    },
    mounted(){
        
    },
    methods :{
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
                }
                
                this.$inertia.get(this.route('admin.sale.pos.index', params), {
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
                }
                
                this.$inertia.get(this.route('admin.sale.pos.index', params), {
                    preserveScroll : true,
                });
            }
        },
        format_date(value) {
            if (value) {
                moment.locale('id');
                return moment(String(value)).format('DD MMM YYYY')
            }
        },
        doSearch: _.throttle(function () {
            this.$inertia.get(this.route('admin.sale.pos.index', {
                search: this.search,
                page : 1,
            }), {
                preserveScroll: true,
            })
        }, 1000),
        select() {
			this.selected = [];
			if (!this.selectAll) {
                this.dataList.data.forEach((value, index) => {
                    this.selected.push(value.id)
                    
                });
			}
		}
    }
}
</script>
