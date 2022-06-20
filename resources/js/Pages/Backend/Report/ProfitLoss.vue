<template>
    <BaseLayout title="Admin | Report Profit / Loss">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Profit / Loss Report
                <div class="float-right">
                    <date-range-picker 
                    ref="picker" 
                    :locale-data="{ firstDay: 1, format: 'dd mmmm yyyy' }" 
                    control-container-class="form-control form-control-sm" 
                    v-model="filterDate"
                    :auto-apply="true"
                    @update="doSearch"
                    opens="left"
                    >
                    </date-range-picker>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="block block-shadow-2 block-rounded">
                        <div class="block-content block-content-full text-right">
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Gross Profit
                            </div>
                            <div class="font-size-h2 font-w700">
                                {{ currency(data.gross_profit) }}
                            </div>
                        </div>
                    </div>

                    <div class="block">
                        <div class="block-content p-0">
                            <table class="table table-bordered">
                                <tr>
                                    <td>
                                        Opening Stock
                                        <small class="text-muted">(By purchase price)</small>
                                    </td>
                                    <td>
                                        {{ currency(data.opening_stock) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Opening Stock
                                        <small class="text-muted">(By sell price)</small>
                                    </td>
                                    <td>
                                        {{ currency(data.opening_stock_by_sp) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Total Purchase
                                        <small class="text-muted">(Exc. tax, Discount)</small>
                                    </td>
                                    <td>
                                        {{ currency(data.total_purchase) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Total Purchase Discount
                                    </td>
                                    <td>
                                        {{ currency(data.total_purchase_discount) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Total Sale Return
                                    </td>
                                    <td>
                                        {{ currency(data.total_sale_return) }}
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td>
                                        Total Stock Adjustment
                                        <small class="text-muted">(Exc. tax, Discount)</small>
                                    </td>
                                    <td>
                                        {{ currency(data.total_stock_adjustment) }}
                                    </td>
                                </tr> -->
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="block block-shadow-2 block-rounded">
                        <div class="block-content block-content-full text-right">
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Net Profit
                            </div>
                            <div class="font-size-h2 font-w700">
                                {{ currency(data.gross_profit) }}
                            </div>
                        </div>
                    </div>
                    <div class="block">
                        <div class="block-content p-0">
                            <table class="table table-bordered">
                                <tr>
                                    <td>
                                        Closing Stock
                                        <small class="text-muted">(By purchase price)</small>
                                    </td>
                                    <td>
                                        {{ currency(data.closing_stock) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Closing Stock
                                        <small class="text-muted">(By sell price)</small>
                                    </td>
                                    <td>
                                        {{ currency(data.closing_stock_by_sp) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Total Sales
                                        <small class="text-muted">(Exc. tax, Discount)</small>
                                    </td>
                                    <td>
                                        {{ currency(data.total_sale) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Total Sale Delivery Charge
                                    </td>
                                    <td>
                                        {{ currency(data.total_sale_shipping) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Total Purchase Return
                                    </td>
                                    <td>
                                        {{ currency(data.total_purchase_return) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import moment from 'moment';
import DateRangePicker from 'vue2-daterange-picker';
import _ from 'lodash';
export default {
    components: {
        BaseLayout,
        DateRangePicker
    },
    data(){
        return {
            filterDate : {
                startDate : this.route().params.from == null ? moment().startOf('year') :  moment(this.route().params.from, 'DD-MM-YYYY'),
                endDate :  this.route().params.to == null ? moment() : moment(this.route().params.to, 'DD-MM-YYYY'),
            },
            configRange : {
                mode: "multiple",
                maxDate: "today",
                dateFormat: "Y-m-d",
            },
        }
    },
    props : {
        data : Object,
    },
    methods:{
        doSearch : _.throttle(function(){
            let params = {};
            if(this.filterDate.startDate && this.filterDate.endDate){
                params = Object.assign(params, {
                    from : moment(this.filterDate.startDate).format('DD-MM-YYYY'),
                    to : moment(this.filterDate.endDate).format('DD-MM-YYYY')
                })
            }
            
            this.$inertia.get(this.route('admin.report.profit_loss', params))
        }, 200),
    }
}
</script>
