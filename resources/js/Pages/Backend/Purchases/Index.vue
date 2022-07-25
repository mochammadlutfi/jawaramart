<template>
    <BaseLayout title="Purchase List">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Purchase Order
                <div class="float-right">
                    <a class="btn btn-sm btn-secondary" :href="route('admin.purchase.order.create')">
                        <i class="si si-plus"></i>
                        Create
                    </a>
                </div>
            </div>
            <base-table :values="dataList" :columns="columns" defaultSort="date" class="table-sm">
                <template #body="data">
                    <tr v-for="value in data.values" :key="value.id">
                        <td>{{ format_date(value.date) }}</td> 
                        <td>{{ value.ref }}</td>
                        <td>{{ value.supplier.name }}</td>
                        <td>
                            <span class="badge badge-secondary" v-if="value.status == 'draft'">Draft</span>
                            <span class="badge badge-warning" v-else-if="value.status == 'pending'">Pending</span>
                            <span class="badge badge-info" v-else-if="value.status == 'ordered'">Ordered</span>
                            <span class="badge badge-success" v-else-if="value.status == 'done'">Done</span>
                            <span class="badge badge-danger" v-else>Cancel</span>
                        </td>
                        <td>
                            <span class="badge badge-success" v-if="value.payment_status == 'paid'">Paid</span>
                            <span class="badge badge-warning" v-else-if="value.payment_status == 'partial'">Partial</span>
                            <span class="badge badge-danger" v-else>Unpaid</span>
                        </td>
                        <td>{{ currency(value.grand_total) }}</td>
                        <td>
                            <b-dropdown text="Action" right class="m-md-2" size="sm">
                                <Link :href="route('admin.purchase.order.show', {id : value.id})" as="button" class="dropdown-item" type="button">
                                    <i class="si si-eye mr-1"></i> Detail
                                </Link>
                                <Link :href="route('admin.purchase.order.edit', {id : value.id})" as="button" class="dropdown-item" type="button">
                                    <i class="si si-note mr-1"></i>Edit
                                </Link>
                            </b-dropdown>
                        </td>
                    </tr>
                </template>
            </base-table>
        </div>
    </BaseLayout>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import moment from 'moment';
import _ from 'lodash';
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import BaseTable from '@/components/Table/BaseTable.vue';
export default {
    components: {
        BaseLayout,
        Link,
        BaseTable
    },
    data(){
        return {
            loading : false,
            selected: [],
            selectAll: false,
            columns : [
                { name : 'Date', field : 'date', width : "15%"},
                { name : 'Vendor Bill', field : 'ref', width : "15%"},
                { name : 'Supplier', field : 'supplier_name', width : "15%"},
                { name : 'Status', field : 'status', width : "15%"},
                { name : 'Payment Status', field : 'payment_status', width : "15%"},
                { name : 'Total', field : 'grand_total', width : "15%"}
            ],
        } 
    },
    props: {
        dataList: Object,
    },
    mounted(){
        
    },
    methods :{
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MMMM YYYY hh:mm')
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
