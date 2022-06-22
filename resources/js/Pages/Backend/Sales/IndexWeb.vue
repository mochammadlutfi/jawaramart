<template>
    <BaseLayout title="List Sales">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                List Web Order ({{ dataList.total }})
                <div class="float-right">
                    <a class="btn btn-sm btn-secondary" :href="route('admin.sale.order.create')">
                        <i class="si si-plus"></i>
                        Create
                    </a>
                </div> 
            </div>
            
            <div class="block block-rounded block-shadow block-bordered d-md-block d-none mb-10">
                <ul class="nav nav-tabs nav-tabs-alt nav-fill">
                    <li class="nav-item">
                        <a class="nav-link" v-bind:class="{ 'active' : (status == 'pending') ? true : false }" :href="route('admin.sale.web')">
                            Pesanan Baru 
                            <span class="badge badge-primary badge-pill">{{ overview.pending }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" v-bind:class="{ 'active' :  (status == 'confirmed') ? true : false }" 
                        :href="route('admin.sale.web', { status : 'confirmed' })">
                            Siap Dikirim <span class="badge badge-primary badge-pill">{{ overview.confirmed }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" v-bind:class="{ 'active' :  (status == 'dikirim') ? true : false }" :href="route('admin.sale.web', { status : 'dikirim' })">
                            Dalam Pengiriman <span class="badge badge-primary badge-pill">{{ overview.dikirim }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" v-bind:class="{ 'active' :  (status == 'selesai') ? true : false }" :href="route('admin.sale.web', { status : 'selesai' })">
                            Selesai <span class="badge badge-primary badge-pill">{{ overview.selesai }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" v-bind:class="{ 'active' :  (status == 'cancel') ? true : false }" :href="route('admin.sale.web', { status : 'cancel' })">
                            Batal <span class="badge badge-primary badge-pill">{{ overview.cancel }}</span>
                        </a>
                    </li>
                </ul>

                <div class="block-content p-2">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="has-search">
                                <i class="fa fa-search"></i>
                                <input type="search" class="form-control" id="search-data-list" v-model="search" @keyup="doSearch()" @change="doSearch()">
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
                                <th>Invoice No</th>
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
                                    <td>{{ data.customer.name }}</td>
                                    <td>
                                        <span class="badge badge-success" v-if="data.payment_status == 'paid'">Paid</span>
                                        <span class="badge badge-info" v-else-if="data.payment_status == 'partial'">Partial</span>
                                        <span class="badge badge-warning" v-else-if="data.payment_status == 'pending'">Pending</span>
                                        <span class="badge badge-danger" v-else>Unpaid</span>
                                    </td>
                                    <td>{{ currency(data.grand_total) }}</td>
                                    <td>{{ data.line_count }}</td>
                                    <th>
                                        <a :href="route('admin.sale.order.show', {id : data.id})" class="btn btn-secondary btn-sm">
                                            <i class="si si-eye mr-1"></i>
                                            Detail
                                        </a>
                                    </th>
                                </tr>
                            </template>
                            <template v-else>
                                <tr>
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
            status : this.route().params.status == undefined ? 'pending' : this.route().params.status,
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
                    status : this.status,
                    search : this.search,
                    page : this.currentPage,
                }
                
                this.$inertia.get(this.route('admin.sale.web', params), {
                    preserveScroll : true,
                });
            }
        },
        prevPage: function() {
            if(this.currentPage > 1){
                this.currentPage--;

                let params = {
                    status : this.status,
                    search : this.search,
                    page : this.currentPage,
                }
                
                this.$inertia.get(this.route('admin.sale.web', params), {
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
            this.$inertia.get(this.route('admin.sale.web', {
                status : this.status,
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
