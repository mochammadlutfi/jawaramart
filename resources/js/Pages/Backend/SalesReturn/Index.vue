<template>
    <BaseLayout title="List Sales">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                List Sales Return ({{ dataList.total }})
            </div>
            
            <base-table :values="dataList" :columns="columns">
                <template #rowCheck>
                    <b-form-checkbox id="selectAll" name="selectAll" @change="selectAll"></b-form-checkbox>
                </template>
                <template #body="data">
                    <tr v-for="value in data.values" :key="value.id">
                        <td>{{ value.date }}</td> 
                        <td>{{ value.ref }}</td>
                        <td>{{ value.sale.ref }}</td>
                        <td>{{ value.sale.customer.name }}</td>
                        <td>
                            <span class="badge badge-success" v-if="value.payment_status == 'paid'">Paid</span>
                            <span class="badge badge-info" v-else-if="value.payment_status == 'partial'">Partial</span>
                            <span class="badge badge-warning" v-else-if="value.payment_status == 'pending'">Pending</span>
                            <span class="badge badge-danger" v-else>Unpaid</span>
                        </td>
                        <td>{{ currency(value.grand_total) }}</td>
                        <td>
                            <a :href="route('admin.sale.return.show', {id : value.id})" class="btn btn-secondary btn-sm">
                                <i class="si si-eye mr-1"></i>
                                Detail
                            </a>
                        </td>
                    </tr>
                </template>
            </base-table>
        </div>
    </BaseLayout>
</template>

<script>
import moment from 'moment';
import _ from 'lodash';
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import vSelect from 'vue-select';
import CustomerSelect  from '@/components/Form/CustomerSelect.vue';
import BaseTable from '@/components/Table/BaseTable.vue';
export default {
    components: {
        BaseLayout,
        BaseTable,
        vSelect,
        CustomerSelect,
    },
    data(){
        return {
            columns : [
                { name : 'Date', field : 'date'},
                { name : 'Return No', field : 'ref'},
                { name : 'Invoice', field : 'sale_ref'},
                { name : 'Customer', field : 'customer_name'},
                { name : 'Payment Status', field : 'payment_status'},
                { name : 'Total', field : 'total_amount'}
            ]
        } 
    },
    props: {
        dataList: Object,
        overview : Array
    },
    methods :{
        format_date(value) {
            if (value) {
                moment.locale('id');
                return moment(String(value)).format('DD MMM YYYY')
            }
        },
    }
}
</script>
