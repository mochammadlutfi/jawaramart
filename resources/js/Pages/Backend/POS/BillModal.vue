<template>
        <b-modal id="invoice" ref="invoice" dialog-class="rounded" body-class="p-0" size="sm" hide-footer hide-header>
            <div class="block block-transparant mb-0" v-if="invoice">
                <div class="block-header border-bottom border-2x">
                    <h3 class="block-title">Invoice</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" @click="$bvModal.hide('invoice')">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content px-0" id="invoice-POS">
                    <div class="info">
                        <img :src="asset('media/logo.png')" class="mb-3"/>
                        <p>{{ $page.props.settings.business_address }}</p>
                    </div>
                    <table class="table table-sm table-invoice">
                        <tr class="pt-0">
                            <td colspan="2" class="pt-0">{{ format_date(invoice.date) }}</td>
                            <td colspan="2" style="text-align:right">{{ format_time(invoice.date) }}</td>
                        </tr>
                        <tr class="pt-0">
                            <td colspan="2" class="pt-0">Reciept Number</td>
                            <td colspan="2" style="text-align:right">{{ invoice.ref }}</td>
                        </tr>
                        <tr class="invoice pt-0">
                            <td colspan="2" class="pt-0">Cashier</td>
                            <td colspan="2" style="text-align:right">{{ invoice.staff.name }}</td>
                        </tr>
                    </table>
                    <table class="table table-sm table-invoice mb-0">
                        <tbody>
                            <template v-for="(d, i) in invoice.line">
                                <tr :key="i">
                                    <td colspan="4" class="pb-0">
                                        <b>{{ d.product.name }}</b>
                                    </td>
                                </tr>
                                <tr :key="'val' + i" class="invoice pt-0">
                                    <td colspan="3">
                                        {{ d.qty }} x {{ currency(d.unit_price) }}
                                    </td>
                                    <td style="text-align:right;vertical-align:bottom">
                                        {{ currency(d.sub_total) }}
                                    </td>
                                </tr>
                            </template>

                            <!-- <tr style="margin-top:10px">
                                <td colspan="3" class="total">{{$t('OrderTax')}}</td>
                                <td style="text-align:right;" class="total">{{invoice_pos.symbol}}
                                    {{ formatNumber(invoice_pos.sale.taxe ,2)}}
                                    ({{ formatNumber(invoice_pos.sale.tax_rate,2)}} %)</td>
                            </tr> -->

                            <!-- <tr style="margin-top:10px">
                                <td colspan="3" class="total">{{$t('Discount')}}</td>
                                <td style="text-align:right;" class="total">{{invoice_pos.symbol}}
                                    {{formatNumber(invoice_pos.sale.discount ,2)}}</td>
                            </tr>

                            <tr style="margin-top:10px">
                                <td colspan="3" class="total">{{$t('Total')}}</td>
                                <td style="text-align:right;" class="total">{{invoice_pos.symbol}}
                                    {{formatNumber(invoice_pos.sale.GrandTotal ,2)}}</td>
                            </tr>

                            <tr v-show="invoice_pos.sale.paid_amount < invoice_pos.sale.GrandTotal">
                                <td colspan="3" class="total">{{$t('Paid')}}</td>
                                <td style="text-align:right;" class="total">{{invoice_pos.symbol}}
                                    {{formatNumber(invoice_pos.sale.paid_amount ,2)}}</td>
                            </tr>

                            <tr v-show="invoice_pos.sale.paid_amount < invoice_pos.sale.GrandTotal">
                                <td colspan="3" class="total">{{$t('Due')}}</td>
                                <td style="text-align:right;" class="total">{{invoice_pos.symbol}}
                                    {{parseFloat(invoice_pos.sale.GrandTotal - invoice_pos.sale.paid_amount).toFixed(2)}}
                                </td>
                            </tr> -->
                        </tbody>
                        <tfoot>
                            <tr class="invoice">
                                <td colspan="3" class="total">Subtotal</td>
                                <td style="text-align:right;" class="total">{{ currency(invoice.grand_total) }}</td>
                            </tr>
                            <tr class="invoice">
                                <td colspan="3" class="total">Total</td>
                                <td style="text-align:right;" class="total">{{ currency(invoice.grand_total) }}</td>
                            </tr>
                            <template v-if="invoice.payment[0].payment_method_id == 1">
                                <tr class="invoice">
                                    <td colspan="3">CASH</td>
                                    <td style="text-align:right;">
                                        {{ currency(invoice.payment[0].amount_received) }}
                                    </td>
                                </tr>
                                <tr class="invoice">
                                    <td colspan="3">CHANGE</td>
                                    <td style="text-align:right;">
                                        {{ currency(invoice.payment[0].change) }}
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr class="invoice">
                                    <td colspan="3">NON CASH</td>
                                    <td style="text-align:right;">
                                        {{ currency(invoice.payment[0].amount) }}
                                    </td>
                                </tr>
                            </template>
                        </tfoot>
                    </table>
                </div>
                <div class="block-content p-5">
                    <div class="row gutter-tiny">
                        <div class="col-6">
                            <button type="button" class="btn btn-primary btn-block btn-noborder" @click="resetPos()">
                                <i class="si si-plus mr-1"></i>
                                ADD MORE
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-primary btn-block btn-noborder" @click="print_pos()">
                                <i class="fi fi-rs-print mr-1"></i>
                                PRINT
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
</template>
<script>
import axios from 'axios';
export default {
    name : 'pos-report-modal',
    data(){
        return {
            payment : null,
            register : null,
            total_payment : 0,
            total_sales : 0,
        }
    },
    methods : {
        openModal(){
            this.fetch();
            this.$bvModal.show('pos-report');
        },
        async fetch(){
            const self = this;
            await axios.get(this.route("admin.sale.pos.report"),{

            })
            .then(function (response) {
                console.log(response.data);
                self.payment = response.data.payment;
                self.register = response.data.register;
                self.total_payment = response.data.total_payment;
                self.total_sales = response.data.total_sales;
            })
            .catch(function (error) {
                
            })
        },
    }
}
</script>