<template>
    <BaseLayout title="List Sales">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                List Sales POS ({{ dataList.total }})
                <div class="float-right">
                    <date-range-picker 
                    ref="picker" 
                    :locale-data="{ firstDay: 1, format: 'dd mmmm yyyy' }" 
                    control-container-class="form-control form-control-sm" 
                    v-model="filter"
                    @update="setFilterDate"
                    :auto-apply="true"
                    opens="left"
                    >
                    </date-range-picker>
                </div>
            </div>
        
            <base-table :values="dataList" :columns="columns" :filter="params" defaultSort="date">
                <template #body="data">
                    <tr v-for="value in data.values" :key="value.id">
                        <td>{{ format_date(value.date) }}</td> 
                        <td>{{ value.ref }}</td>
                        <td>{{ value.customer }}</td>
                        <td>
                            <span class="badge badge-success" v-if="value.payment_status == 'paid'">Paid</span>
                            <span class="badge badge-info" v-else-if="value.payment_status == 'partial'">Partial</span>
                            <span class="badge badge-warning" v-else-if="value.payment_status == 'pending'">Pending</span>
                            <span class="badge badge-danger" v-else>Unpaid</span>
                        </td>
                        <td>{{ currency(value.grand_total) }}</td>
                        <td>
                            <a :href="route('admin.sale.order.show', {id : value.id})" class="btn btn-secondary btn-sm">
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
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import DateRangePicker from 'vue2-daterange-picker';
import BaseTable from '@/components/Table/BaseTable.vue';
export default {
    components: {
        BaseLayout,
        DateRangePicker,
        BaseTable,
    },
    data(){
        return {
            columns : [
                {
                    name : "Date",
                    field : "date",
                    width : "15%",
                },
                {
                    name : 'Invoice No',
                    field : 'ref',
                    width : "20%",
                },
                {
                    name : "Customer",
                    field : 'customer',
                    width : "25%",
                },
                {
                    name : "Payment Status",
                    field : 'payment_status',
                    width : "15%",
                },
                {
                    name : "Total Amount",
                    field : 'grand_total',
                    width : "15%",
                }
            ],
            filter : {
                startDate : this.route().params.startDate == undefined ? moment().startOf('month') :  moment(this.route().params.startDate, 'DD-MM-YYYY'),
                endDate : this.route().params.endDate == undefined ? moment() : moment(this.route().params.endDate, 'DD-MM-YYYY'),
            },
            configRange : {
                mode: "multiple",
                maxDate: "today",
                dateFormat: "Y-m-d",
            },
            params : {
                startDate : this.route().params.startDate == undefined ? moment().startOf('month').format('DD-MM-YYYY').toString() :  moment(this.route().params.startDate, 'DD-MM-YYYY').format('DD-MM-YYYY').toString(),
                endDate : this.route().params.endDate == undefined ? moment().format('DD-MM-YYYY').toString() : moment(this.route().params.endDate, 'DD-MM-YYYY').format('DD-MM-YYYY').toString(),
            }
        } 
    },
    props: {
        dataList: Object,
        overview : Array
    },
    mounted(){
        
    },
    methods :{
        format_date(value) {
            if (value) {
                moment.locale('id');
                return moment(String(value)).format('DD/MM/YYYY hh:mm')
            }
        },
        setFilterDate() {
            if(this.filter.startDate && this.filter.endDate){
                this.params.startDate = moment(this.filter.startDate).format('DD-MM-YYYY').toString();
                this.params.endDate = moment(this.filter.endDate).format('DD-MM-YYYY').toString();
            }
        },
    }
}
</script>
