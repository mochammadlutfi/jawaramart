<template>
    <BaseLayout title="Purchase List">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Purchase Order
                <div class="float-right">
                    <a class="btn btn-sm btn-secondary" :href="route('admin.purchase.order.create')" v-if="hasPermission('Purchase Order', 'create')">
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
                                    <Link :href="dataList.prev_page_url ? dataList.prev_page_url : '#'" as="button" class="btn btn-alt-secondary mx-1" type="button"
                                    :disabled="dataList.prev_page_url ? false : true">
                                        <i class="fa fa-chevron-left fa-fw"></i>
                                    </Link>
                                    <Link :href="dataList.next_page_url ? dataList.next_page_url : '#'" as="button" class="btn btn-alt-secondary mx-1" type="button"
                                    :disabled="dataList.next_page_url ? false : true">
                                        <i class="fa fa-chevron-right fa-fw"></i>
                                    </Link>
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
                                <th>Status</th>
                                <th>Payment Status</th>
                                <th>Total Amount</th>
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
                                    <td>{{ data.supplier.name }}</td>
                                    <td>
                                        <span class="badge badge-secondary" v-if="data.status == 'draft'">Draft</span>
                                        <span class="badge badge-warning" v-else-if="data.status == 'pending'">Pending</span>
                                        <span class="badge badge-info" v-else-if="data.status == 'ordered'">Ordered</span>
                                        <span class="badge badge-success" v-else-if="data.status == 'done'">Done</span>
                                        <span class="badge badge-danger" v-else>Cancel</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success" v-if="data.payment_status == 'paid'">Paid</span>
                                        <span class="badge badge-warning" v-else-if="data.payment_status == 'partial'">Partial</span>
                                        <span class="badge badge-danger" v-else>Unpaid</span>
                                    </td>
                                    <td>{{ currency(data.grand_total) }}</td>
                                    <th>
                                        <b-dropdown text="Action" right class="m-md-2" size="sm">
                                            <Link :href="route('admin.purchase.order.show', {id : data.id})" as="button" class="dropdown-item" type="button">
                                                <i class="si si-eye mr-1"></i> Detail
                                            </Link>
                                            <Link :href="route('admin.purchase.order.edit', {id : data.id})" as="button" class="dropdown-item" type="button">
                                                <i class="si si-note mr-1"></i>Edit
                                            </Link>
                                        </b-dropdown>
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
import hasPermission from '@/Utils/permission';
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
            search : this.route().params.search == '' ? '' : this.route().params.search
        } 
    },
    props: {
        dataList: Object,
    },
    mounted(){
        
    },
    methods :{
        hasPermission,
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MMM YYYY')
            }
        },
        doSearch : _.throttle(function(){
            let params = {};
            

            this.$inertia.get(this.route('admin.purchase.order.index', { search : this.search }), {
                preserveScroll : true,
            })
        }, 200),
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
