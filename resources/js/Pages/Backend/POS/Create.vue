<template>
    <div id="page-container" class="main-content-boxed page-header-fixed side-scroll">
        
        <!-- Header -->
        <header id="page-header">
            <!-- Header Content -->
            <div class="content-header pos">
                <!-- Left Section -->
                <div class="content-header-section">
                    <!-- Logo -->
                    <div class="content-header-item height-50">
                        <a :href="route('admin.sale.pos.index')">
                            <img :src="asset($page.props.settings.app_logo)" class="img-fluid h-100">
                        </a>
                    </div>
                    <!-- END Logo -->
                </div>
                <!-- END Left Section -->
                

                <!-- Right Section -->
                <div class="content-header-section">
                    
                    <button type="button" class="btn btn-outline-primary mx-1 btn-sm" @click="openReport">
                        <i class="fa fa-cash-register"></i>
                        Cash Register
                    </button>

                    <button type="button" class="btn btn-outline-primary mx-1 btn-sm" @click="openTransaction">
                        <i class="fi fi-rs-time-twenty-four"></i>
                        Recent Transaction
                    </button>

                    <b-dropdown variant="dual link" class="d-inline-block ml-2" menu-class="p-0 border-0 dropdown-menu-md" right no-caret ref="oneDropdownDefaultUser">
                        <template #button-content>
                            <div class="d-flex align-items-center">
                                <img class="img-avatar img-avatar32" :src="$page.props.auth.staff.avatar_url" alt="Header Avatar" style="width: 30px;">
                                <span class="d-none d-sm-inline-block ml-2">{{ $page.props.auth.staff.name }}</span>
                                <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block ml-1 mt-1"></i>
                            </div>
                        </template>
                        <div class="p-3 text-center bg-primary rounded-top">
                            <img class="img-avatar img-avatar48 img-avatar-thumb" :src="$page.props.auth.staff.avatar_url" alt="">
                            <p class="mt-2 mb-0 text-white font-w500">{{ $page.props.auth.staff.name }}</p>
                        </div>
                        <div class="p-2">
                            <Link class="dropdown-item d-flex align-items-center justify-content-between" :href="route('admin.profile')">
                                <span class="font-size-sm font-w500">Profile</span>
                            </Link>
                            <Link :href="route('admin.logout')" method="post" as="button" class="dropdown-item d-flex align-items-center justify-content-between">
                                <span class="font-size-sm font-w500">Log Out</span>
                            </Link >
                        </div>
                    </b-dropdown>
                </div>
                <!-- END Right Section -->
            </div>
            <!-- END Header Content -->

        </header>
        <!-- END Header -->

        <!-- Main Container -->
        <main id="main-container">
            <div class="content pos">
                <div class="row gutters-tiny">
                    <div class="col-lg-6">
                        <div class="block block-rounded block-shadow block-bordered">
                            <div class="block-content pt-2 px-3">
                                <CustomerSelect @done="(customer) => form.customer_id = customer" :default="true"/>
                            </div>
                            <div class="block-content px-3 pb-15">
                                <div class="row">
                                    <div class="col-12" style="min-height:59vh">
                                        <table class="border-bottom border-top font-size-sm mb-3 table table-vcenter">
                                            <thead>
                                                <tr class="text-center">
                                                    <th class="px-0" width="40%">Product</th>
                                                    <th width="24%">Quantity</th>
                                                    <th>Price</th>
                                                    <th>Subtotal</th>
                                                    <th width="10"><i class="si si-close text-danger"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template v-if="lines.length">
                                                <tr v-for="(line, index) in lines" :key="index">
                                                    <td class="px-0">
                                                        <a href="javascript:void(0)" @click="ModalUpdateProduct(line)" class="link-effect font-w700">{{ line.name }}</a>
                                                    </td>
                                                    <td>
                                                        <div class="input-group product-qty product-qty-sm">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-noborder btn-primary btn-sm m-0" type="button" @click="decrement(line.variant_id)">
                                                                    <i class="fa fa-minus"></i>
                                                                </button>
                                                            </span>
                                                            <input type="text" v-model="line.qty" class="form-control input-number text-center form-control-sm" @change="stockCheck($event, line.variant_id)">
                                                            <span class="input-group-btn">
                                                                <button class="btn btn-noborder btn-primary btn-sm m-0" type="button" @click="increment(line.variant_id)">
                                                                    <i class="fa fa-plus"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        {{ currency(line.net_price) }}
                                                    </td>
                                                    <td>
                                                        {{ currency(line.price * line.qty) }}
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-danger btn-noborder" @click="deleteCart(line.variant_id)" title="Delete">
                                                            <i class="si si-close"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <table class="border-bottom table table-sm">
                                            <tr>
                                                <td colspan="2" class="px-0">
                                                    <div class="font-w600 font-size-md">Total</div>
                                                </td>
                                                <td colspan="2">
                                                    <div class="font-w600 font-size-md text-right">{{ currency(form.total) }}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="px-0">
                                                    <span class="font-w600 font-size-md">Discount</span>
                                                    <button class="btn btn-sm btn-secondary" type="button" @click="$bvModal.show('discount-sale')"><i class="si si-note"></i></button>
                                                </td>
                                                <td colspan="2">
                                                    <div class="font-w600 font-size-md text-right">{{ currency(form.discount_amount) }}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="px-0">
                                                    <div class="font-w600 font-size-md">Grand Total</div>
                                                </td>
                                                <td colspan="2">
                                                    <div class="font-w600 font-size-md text-right">{{ currency(form.grand_total) }}</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <button class="btn btn-lg btn-block btn-danger" @click.prevent="openInvoice(4)">
                                            Reset
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-lg btn-block btn-primary" @click.prevent="openPayment" :disabled="!lines.length">
                                            Pay Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <pos-product @select="(data) => addCart(data)" ref="productList"></pos-product>
                    </div>
                </div>
            </div>
        </main>
        <!-- End Main Container -->

        <b-modal id="discount-sale" ref="discount-sale" size="md" content-class="rounded" centered body-class="p-0" hide-footer hide-header>
            <div class="block block-rounded block-transparent mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Discount</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" @click="$bvModal.hide('discount-sale')">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content font-size-sm">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="field-discount_type">Discount Type</label>
                                <b-form-select v-model="form.discount_type" id="field-discount_type" name="discount_type">
                                    <b-form-select-option value="fixed">Fixed</b-form-select-option>
                                    <b-form-select-option value="percentage">Percentage</b-form-select-option>
                                </b-form-select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="field-discount_amount">Discount Amount</label>
                                <input type="number" min="0" max="100" class="form-control" v-model.number="form.discount_value" id="field-discount_amount" v-if="form.discount_type == 'percentage'">
                                <CurrencyInput v-model.lazy="form.discount_amount" id="field-discount_amount" class="form-control" v-else/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>

        <b-modal id="payment" ref="payment" size="md" content-class="rounded" body-class="p-0" no-close-on-backdrop hide-footer hide-header>
            <div class="block block-rounded block-transparent mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">{{ currency(form.grand_total) }}</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" @click="$bvModal.hide('payment')">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content font-size-sm">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="payment-methods">Payment Method</label>
                                <!-- <div class="radio-box-group row">
                                    <label class="radio-box-container col" v-for="method in payment_methods" :key="method.id">
                                        <input type="radio" name="radio" v-model="form.payment_method" :value="method.id">
                                        <div class="radio-content">
                                            <img :src="method.image_url" alt="" class="img-fluid" v-if="method.image">
                                            <h3 v-else>{{ method.name }}</h3>
                                        </div>
                                    </label>
                                </div> -->
                                <div class="form-group">
                                    <v-select label="name"
                                    :options="payment_methods"
                                    :get-option-label="(option) => option.name"
                                    v-bind:class="{'is-invalid' : errors }"
                                    :reduce="payment_methods => payment_methods.id"
                                    v-model="form.payment_method">
                                        <template slot="option" slot-scope="option">
                                            {{ option.name }}
                                        </template>
                                    </v-select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="payment-recieved">Amount Received</label>
                                <CurrencyInput v-model="form.payment_received" id="payment-recieved" class="form-control"/>
                            </div>
                            <div class="form-group" v-if="form.payment_method == 1">
                                <label for="payment-change">Change</label>
                                <div>{{ currency(form.payment_change) }}</div>
                            </div>
                            <div class="form-group" v-if="form.payment_method != 1">
                                <label for="payment-ref">Transaction Ref</label>
                                <input type="text" class="form-control" v-model="form.payment_note" id="payment-ref"/>
                            </div>
                            <div class="form-group">
                                <label for="payment-note">Payment Note</label>
                                <textarea class="form-control" v-model="form.payment_note" id="payment-note"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-noborder btn-block" @click="submitPOS">
                    <i class="fa fa-check"></i> Charge
                </button>
            </div>
        </b-modal>

        <b-modal id="product-info" ref="product-info" size="md" content-class="rounded" body-class="p-0" centered hide-footer hide-header no-close-on-backdrop>
            <form @submit.prevent="updateProduct">
                <div class="block block-rounded block-transparent mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">{{ detail.name }}</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" @click="$bvModal.hide('product-info')">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content font-size-sm">
                        <div class="form-group">
                            <label for="product-price">Unit Price</label>
                            <CurrencyInput v-model="detail.price" id="product-price" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label for="product-discount_type">Discount Type</label>
                            <b-form-select v-model="detail.discount_type" id="product-discount_type">
                                <b-form-select-option value="fixed">Fixed</b-form-select-option>
                                <b-form-select-option value="percentage">Percentage (%)</b-form-select-option>
                            </b-form-select>
                        </div>
                        <div class="form-group">
                            <label for="product-discount_amount">Discount Amount</label>
                            <input type="number" min="0" max="100" class="form-control" v-model.number="detail.discount_value" id="product-discount_amount" v-if="detail.discount_type == 'percentage'">
                            <CurrencyInput v-model.lazy="detail.discount_amount" id="product-discount_amount" class="form-control" v-else/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-noborder" @click="$bvModal.hide('product-info')">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-noborder">
                        <i class="fa fa-check"></i> Update
                    </button>
                </div>
            </form>
        </b-modal>
        
        <b-modal id="open-cash" ref="openCash" size="md" content-class="rounded" body-class="p-0" no-close-on-backdrop hide-footer centered hide-header>
            <div class="block block-rounded block-transparent mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Open Cash Register</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" @click="$bvModal.hide('payment')">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content font-size-sm">
                    <form @submit.prevent="openRegister">
                        <div class="form-group">
                            <label for="field-cash_amount">Cash on Hand</label>
                            <CurrencyInput v-model="register_amount" id="field-cash_amount" class="form-control"/>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-noborder btn-block">
                                <i class="fa fa-check"></i> Open Register
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </b-modal>

        <!-- Modal Show Invoice POS-->
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
        
        <report-modal ref="posReport" :register="cash_register" @closed="(value) => cash_register = null"/>

        <pos-transaction ref="posTransaction"/>
    </div>
</template>
<style lang="scss">

.info {
    text-align: center;
    margin-bottom: 15px;

    p {
        font-size: 11px;
    }

    img {
        width: 75%;
    }
}

.table-invoice {
    th, td {
        border: none !important;
    }
    tr.invoice td {
        border-bottom : 1px dashed black !important;
    }
}

.radio-box-group {
    display: flex;
}


</style>
<script>
import PosProduct from './Product.vue';
import ProductQty from "@/components/Product/ProductQty.vue";
import CurrencyInput  from '@/components/Form/CurrencyInput.vue';
import CustomerSelect  from '@/components/Form/CustomerSelect.vue';
import vueEasyPrint from "vue-easy-print";
import axios from 'axios';
import moment from 'moment';
import reportModal from './ReportModal.vue';
import PosTransaction from './RecentTransaction.vue';
import vSelect from 'vue-select';
export default {
    components: {
        PosProduct,
        ProductQty,
        CustomerSelect,
        CurrencyInput,
        vueEasyPrint,
        reportModal,
        vSelect,
        PosTransaction
    },
    props:{
        cash_register : Object,
        payment_methods : Array,
    },
    mounted(){
        if(!this.cash_register){
            this.$bvModal.show('open-cash');
        }
    },
    data(){
        return {
            lines : [],
            audio: new Audio(window._asset + "media/audio/Beep.wav"),
            register_amount : 0,
            form : {
                discount_type: 'fixed',
                discount_value : 0,
                discount_amount : 0,
                total : 0,
                grand_total : 0,
                payment_method : 1,
                payment_received : null,
                payment_change : 0,
            },
            detail :{
                id : null,
                variant_id : null,
                name : null,
                price : null,
                qty : null,
                discount_type : null,
                discount_amount : null,
                discount_value : null,
                subtotal : null,
                net_price : null,
                max_stock : null,
            },
            invoice : null,
            errors : null,
        }
    },
    watch :{
        form: {
            deep: true,
            handler(val){
                if(val.discount_type == 'percentage' && val.discount_value){
                    if(val.discount_value > 100){
                        val.discount_value = 100;
                    }
                    val.discount_amount  =  val.total *  val.discount_value / 100;
                }
                if(val.discount_type == 'fixed'){
                    if(val.discount_amount > val.total){
                        val.discount_amount = val.total;
                    }
                    val.discount_value = 0;
                }

                // if(val.payment_received){
                    val.payment_change = val.payment_received - val.grand_total;
                // }

                val.grand_total = val.total - val.discount_amount;
            }
        }
    },
    methods: {
        //----------- Calcul Total
        CaclulTotal() {
            this.form.total = 0;
            for (var i = 0; i < this.lines.length; i++) {
                // var tax = this.lines[i].taxes * this.lines[i].qty;
                this.lines[i].subtotal = this.lines[i].qty * this.lines[i].price;

                this.form.total = this.form.total + this.lines[i].subtotal;
            }

            // const total_without_discount = parseFloat(
            //     this.total - this.sale.discount
            // );

            // this.sale.TaxNet = parseFloat(
            //     (total_without_discount * this.sale.tax_rate) / 100
            // );

            // this.GrandTotal = parseFloat(
            //     total_without_discount + this.sale.TaxNet + this.sale.shipping
            // );

            // var grand_total = this.GrandTotal.toFixed(2);

            this.form.grand_total = this.form.total - this.form.discount_amount;
        },

        addCart(product){
            if(this.lines.length >= 1){
                if(this.lines.some(detail => detail.variant_id === product.variant_id)){
                    this.increment_qty_scanner(product.variant_id);
                }else{
                    this.audio.play();
                    this.lines.push(product);
                }
            }else{
                this.audio.play();
                this.lines.push(product);
            }
            this.CaclulTotal();
            this.$forceUpdate();
        },

        //-----------------------------------Delete Detail Product ------------------------------\\
        deleteCart(variant_id) {
            for (var i = 0; i < this.lines.length; i++) {
                if (variant_id === this.lines[i].variant_id) {
                    this.lines.splice(i, 1);
                    this.CaclulTotal();
                }
            }
        },
        
        //----------------------------------- Increment QTY with barcode scanner ------------------------------\\
        increment_qty_scanner(variant_id) {
            console.log(variant_id)
            for (var i = 0; i < this.lines.length; i++) {
                if (this.lines[i].variant_id === variant_id) {
                    if (this.lines[i].qty + 1 > this.lines[i].max_stock) {
                        this.$swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: `Only ${ this.lines[i].max_stock } left in stock`,
                            showConfirmButton : false,
                            showCancelButton: false,
                            timer : 1500,
                        });
                    } else {
                        this.lines[i].qty++;
                    // console.log('add');
                    }
                }
            }
            // this.CaclulTotal();
            this.$forceUpdate();
        },

        //----------------------------------- Increment QTY ------------------------------\\
        increment(variant_id) {
            for (var i = 0; i < this.lines.length; i++) {
                if (this.lines[i].variant_id == variant_id) {
                    if (this.lines[i].qty + 1 > this.lines[i].max_stock) {
                        this.$swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: `Only ${ this.lines[i].max_stock } left in stock`,
                            showConfirmButton : false,
                            showCancelButton: false,
                            timer : 1500,
                        });
                    } else {
                        this.lines[i].qty++;
                    }
                }
            }
            this.CaclulTotal();
            this.$forceUpdate();
        },

        //----------------------------------- Stock QTY ------------------------------\\
        stockCheck(e, variant_id) {
            var current = parseInt(e.target._value);
            for (var i = 0; i < this.lines.length; i++) {
                if (this.lines[i].variant_id == variant_id) {
                    if (current > this.lines[i].max_stock) {
                        this.lines[i].qty = this.lines[i].max_stock;
                    }else if(current < 1){
                        this.lines[i].qty = 1
                    }else{
                        this.lines[i].qty = current;
                    }
                }
            }
            this.CaclulTotal();
            this.$forceUpdate();
        },

        //----------------------------------- decrement QTY ------------------------------\\
        decrement(variant_id) {
            for (var i = 0; i < this.lines.length; i++) {
                if (this.lines[i].variant_id == variant_id) {
                    if (this.lines[i].qty - 1 < 1) {
                        this.deleteCart(variant_id)
                    //     this.makeToast("warning", this.$t("LowStock"), this.$t("Warning"));
                    } else {
                        this.lines[i].qty--;
                    }
                }
            }
            this.CaclulTotal();
            this.$forceUpdate();
        },

        ModalUpdateProduct(detail){
            this.detail = {};
            this.detail = detail;
            // this.detail.id = detail.id;
            // this.detail.variant_id = detail.variant_id;
            // this.detail.name = detail.name;
            // this.detail.price = detail.price;
            // this.detail.qty = detail.qty;
            // this.product.fix_price = product.fix_price;
            // this.product.fix_stock = product.fix_stock;
            // this.product.current = product.current;
            // this.product.tax = product.tax;
            // this.product.discount_Method = product.discount_Method;
            // this.product.discount = product.discount;
            // this.product.tax_percent = product.tax_percent;
            this.$bvModal.show("product-info");

        },

        updateProduct(){
            for (var i = 0; i < this.lines.length; i++) {
                if (this.lines[i].variant_id === this.detail.variant_id) {

                    if (this.lines[i].max_stock < this.lines[i].qty) {
                        this.lines[i].qty = this.lines[i].max_stock;
                    } else {
                        this.lines[i].qty = 1;
                    }

                    this.lines[i].price = this.detail.price;
                    this.lines[i].net_price = this.detail.price;
                    this.lines[i].discount_type = this.detail.discount_type;
                    this.lines[i].discount_amount = this.detail.discount_amount;
                    this.lines[i].discount_value = this.detail.discount_value;

                    if (this.lines[i].discount_type == "fixed") {
                        //Fixed
                        this.lines[i].discount_value = null;
                        this.lines[i].discount_amount = this.detail.discount_amount;
                    } else {
                        //Percentage %
                        this.lines[i].discount_amount = (this.detail.price * this.detail.discount_value) / 100;
                    }

                    if(this.lines[i].discount_amount){
                        this.lines[i].net_price = this.detail.price - this.detail.discount_amount;
                    }
                    

                    // if (this.details[i].tax_method == "1") {
                    //     //Exclusive
                    //     this.details[i].Net_price = parseFloat(
                    //         this.details[i].Unit_price - this.details[i].DiscountNet
                    //     );

                    //     this.details[i].taxe = parseFloat(
                    //         (this.details[i].tax_percent *
                    //             (this.details[i].Unit_price - this.details[i].DiscountNet)) /
                    //         100
                    //     );
                    //     this.details[i].Total_price = parseFloat(
                    //         this.details[i].Net_price + this.details[i].taxe
                    //     );
                    // } else {
                    //     //Inclusive
                    //     this.details[i].Net_price = parseFloat(
                    //         (this.details[i].Unit_price - this.details[i].DiscountNet) /
                    //         (this.details[i].tax_percent / 100 + 1)
                    //     );

                    //     this.details[i].taxe = parseFloat(
                    //         this.details[i].Unit_price -
                    //         this.details[i].Net_price -
                    //         this.details[i].DiscountNet
                    //     );
                    //     this.details[i].Total_price = parseFloat(
                    //         this.details[i].Net_price + this.details[i].taxe
                    //     );
                    // }

                    this.$forceUpdate();
                }
            }
            this.CaclulTotal();
            this.$bvModal.hide("product-info");
        },

        openPayment(){
            this.form.payment_received = this.form.grand_total;
            this.$bvModal.show('payment');
        },

        submitPOS(){
            let data = {};
            data = Object.assign(data, this.form)
            let lines = {
                lines : this.lines
            }
            data = Object.assign(data, lines)
            
            this.$swal.fire({
                title: 'Tunggu Sebentar...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            axios.post(this.route('admin.sale.pos.store'), data)
            .then((res) => {
                console.log(res);
                if(res.data.fail){
                    this.errors = res.data.errors;
                    this.$swal.close();
                }else{
                    this.$swal.close();
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        showConfirmButton: false,
                        showCancelButton: false,
                        html: `Sale Added Sucessfully!`,
                        timer: 1500
                    });
                    this.$refs['payment'].hide();
                    this.openInvoice(res.data.id);
                    this.resetPos();
                }
            })
            .catch(function (error) {
                if (error.response) {
                console.log(error.response.data);
                console.log(error.response.status);
                console.log(error.response.headers);
                }
            });
        },

        openInvoice(id){
            axios.get(this.route('admin.sale.pos.invoice_print', id))
            .then( response => {
                this.invoice = response.data;
                this.$bvModal.show('invoice');
                setTimeout(() => this.print_pos(), 1000);
            })
            .catch(() => {
                // Complete the animation of the  progress bar.
                // setTimeout(() => NProgress.done(), 500);
            });
        },
        
        //------------------------------ Print -------------------------\\
        print_pos() {
            var divContents = document.getElementById("invoice-POS").innerHTML;
            var a = window.open("", "", "height=500, width=250");
            var style = window._asset + 'css/pos_print.css';
            a.document.write(
                `<link rel="stylesheet" href="${ style }"/>`
            );
            a.document.write("<body>");
            a.document.write(divContents);
            a.document.write("</body></html>");
            a.document.close();
            // a.print();
        },

        resetPos(){
            this.form.customer_id = 1;
            this.form.discount_amount = 0;
            this.form.discount_type = "fixed";
            this.form.discount_value = 0;
            this.form.grand_total = 0;
            this.form.payment_change = 0;
            this.form.payment_method = 1;
            this.form.payment_received = null;
            this.lines = [];
            this.$bvModal.hide('invoice');
            this.$refs.productList.data();
        },

        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MMM YYYY')
            }
        },

        format_time(value){
            if (value) {
                return moment(String(value)).format('hh:mm')
            }
        },

        openRegister(){
            this.$swal.fire({
                title: 'Tunggu Sebentar...',
                text: '',
                imageUrl: window._asset + 'media/loading.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
            });
            this.$inertia.post(this.route('admin.sale.pos.open'), {
                    amount : this.register_amount
                }, {
                preserveScroll: true,
                onProgress: ()=> {
                },
                onSuccess: () => {
                    this.$bvModal.hide('open-cash');
                    this.$swal.close();
                },
                onError:() => {
                    this.$swal.close();
                }
            })
        },
    
        openReport(value){
            this.$refs.posReport.openModal(value);
        },

        
        openTransaction(){
            this.$refs.posTransaction.openModal();
        },

    }
}
</script>
