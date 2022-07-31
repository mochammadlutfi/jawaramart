<template>
    <BaseLayout title="List Sales">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                List Purchase Return ({{ dataList.total }})
            </div>

            <base-table :values="dataList" :columns="columns" defaultSort="date" class="table-sm">
                <template #body="data">
                    <tr v-for="value in data.values" :key="value.id">
                        <td>{{ format_date(value.date) }}</td> 
                        <td>{{ value.ref }}</td>
                        <td>{{ value.purchase.ref }}</td>
                        <td>{{ value.supplier.name }}</td>
                        <td>
                            <span class="badge badge-secondary" v-if="value.status == 'draft'">Draft</span>
                            <span class="badge badge-warning" v-else-if="value.status == 'pending'">Pending</span>
                            <span class="badge badge-info" v-else-if="value.status == 'ordered'">Ordered</span>
                            <span class="badge badge-success" v-else-if="value.status == 'done'">Done</span>
                            <span class="badge badge-danger" v-else>Cancel</span>
                        </td>
                        <td>
                            <span class="badge badge-success" v-if="data.payment_status == 'paid'">Paid</span>
                            <span class="badge badge-info" v-else-if="data.payment_status == 'partial'">Partial</span>
                            <span class="badge badge-warning" v-else-if="data.payment_status == 'pending'">Pending</span>
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
import vSelect from 'vue-select';
import BaseTable from '@/components/Table/BaseTable.vue';
export default {
    components: {
        BaseLayout,
        Link,
        vSelect,
        BaseTable,
    },
    data(){
        return {
            loading : false,
            selected: [],
            selectAll: false,
            columns : [
                { name : 'Date', field : 'date', width : "15%"},
                { name : 'Return No', field : 'ref', width : "15%"},
                { name : 'Source No', field : 'po_ref', width : "15%"},
                { name : 'Supplier', field : 'supplier_name', width : "15%"},
                { name : 'Payment Status', field : 'payment_status', width : "15%"},
                { name : 'Total', field : 'grand_total', width : "15%"}
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
        
    }
}
</script>
