<template>
    <BaseLayout title="Detail Kreator">
        <div class="content">
            <div class="block">
                <div class="block-content block-content-full">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="d-flex">
                                <img class="img-avatar img-avatar128" :src="data.avatar_url" alt="">
                                <div class="ml-lg-4 my-lg-auto">
                                    <div class="font-size-h4 font-w700 mb-0">{{ data.name }}</div>
                                    <div class="font-size-h5 text-muted font-w600 mb-0">{{ data.email }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 border-left">
                            <div class="form-group row mb-0">
                                <label class="col-lg-3 col-form-label" for="example-hf-email">No Handphone</label>
                                <div class="col-lg-7">
                                    <div class="form-control-plaintext">{{ data.phone }}</div>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-3 col-form-label" for="example-hf-email">Email Address</label>
                                <div class="col-lg-7">
                                    <div class="form-control-plaintext">: {{ data.email }}</div>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-3 col-form-label" for="example-hf-email">Registered at</label>
                                <div class="col-lg-7">
                                    <div class="form-control-plaintext">{{ format_date(data.created_at) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="content-heading pt-0 mb-3">
                Recent Transaction
            </div>
            <div class="block block-rounded block-shadow-2 block-bordered mb-5">
                <div class="block-content px-0 py-0">
                    <table class="table table-striped table-vcenter table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Status</th>
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
                            <template v-if="sales.length">
                                <tr v-for="s in sales" :key="s.id">
                                    <td>{{ s.date }}</td> 
                                    <td>{{ s.ref }}</td>
                                    <td>
                                        <span class="badge badge-success" v-if="s.status == 'paid'">Done</span>
                                        <span class="badge badge-info" v-else-if="s.status == 'partial'">Progress</span>
                                        <span class="badge badge-warning" v-else-if="s.status == 'pending'">Pending</span>
                                        <span class="badge badge-danger" v-else>Cancel</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success" v-if="s.payment_status == 'paid'">Paid</span>
                                        <span class="badge badge-info" v-else-if="s.payment_status == 'partial'">Partial</span>
                                        <span class="badge badge-warning" v-else-if="s.payment_status == 'pending'">Pending</span>
                                        <span class="badge badge-danger" v-else>Unpaid</span>
                                    </td>
                                    <td>{{ currency(s.grand_total) }}</td>
                                    <td>{{ s.line_count }}</td>
                                    <th>
                                        <a :href="route('admin.sale.order.show', {id : s.id})" class="btn btn-secondary btn-sm">
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
                                            <h3 class="font-size-24 font-w600 mt-3">Data Tidak Ditemukan</h3>
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
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import moment from 'moment';
import _ from 'lodash';

export default {
    components: {
        BaseLayout,
    },
    props : {
        data : Object,
        sales : Array,
    },
    data() {
        return {
            loading: false,
            selected: [],
            selectAll: false,
            search : this.route().params.search == undefined ? '' : this.route().params.search,
            currentPage: this.route().params.page == undefined ? 1 : this.route().params.page,
        }
    },
    methods : {
        format_date(value) {
            if (value) {
                moment().locale('id');
                return moment(String(value)).format('DD MMMM YYYY')
            }
        },
        checkPaginate(type){
            const vm = this;
            if(vm.sales){
                if(type == 'next'){
                    return (vm.sales.next_page_url) ? false : true;
                }else{
                    return (vm.sales.prev_page_url) ? false : true;
                }
            }else{
                return true;
            }
        },
        nextPage: function() {
            if(this.currentPage < this.sales.total){
                this.currentPage++;

                let params = {
                    search : this.search,
                    page : this.currentPage,
                }
                
                this.$inertia.get(this.route('admin.customer.index', params), {
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
                
                this.$inertia.get(this.route('admin.customer.index', params), {
                    preserveScroll : true,
                });
            }
        },
        doSearch: _.throttle(function () {
            this.$inertia.get(this.route('admin.customer.index', {
                search: this.search,
                page : 1,
            }), {
                preserveScroll: true,
            })
        }, 50000),
    }
}
</script>
