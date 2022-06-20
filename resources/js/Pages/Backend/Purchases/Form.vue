<template>
    <BaseLayout :title="title">
        <div class="content">
            <form @submit.prevent="submit">
                <div class="content-heading pt-0 mb-3">
                    {{ title }}
                    <div class="float-right">
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="si si-paper-plane mr-1"></i>
                            Save
                        </button>
                    </div>
                </div>
                <div class="block block-bordered block-shadow block-rounded">
                    <div class="block-content pb-15">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="field-supplier">Supplier</label>
                                    <supplier-select @done="(value) => form.supplier_id = value" :data="(editMode) ? data.supplier : null" :error="errors.supplier_id"/>
                                    <div class="invalid-feedback" v-if="errors.supplier_id">{{ errors.supplier_id[0] }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="field-ref">Reference No</label>
                                    <input type="text" v-bind:class="{'form-control':true, 'is-invalid' : errors.ref}" id="field-ref" v-model="form.ref" >
                                    <div id="error-ref" class="invalid-feedback"></div>
                                </div>
                                <!-- <div class="form-group">
                                    <label for="field-status">Status</label>
                                    <b-form-select v-model="form.status" id="field-status" name="status">
                                        <b-form-select-option value="draft">Draft</b-form-select-option>
                                        <b-form-select-option value="pending">Pending</b-form-select-option>
                                        <b-form-select-option value="recieved">Recieved</b-form-select-option>
                                        <b-form-select-option value="cancel">Cancel</b-form-select-option>
                                    </b-form-select>
                                </div> -->
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="field-date">Date</label>
                                    <div class="has-search">
                                        <i class="si si-calendar"></i>
                                        <flat-pickr class="bg-white" :config="config" v-bind:class="{'form-control':true , 'is-invalid' : errors.date}" v-model="form.date"></flat-pickr>
                                    </div>
                                    <div class="invalid-feedback" v-if="errors.date">{{ errors.date[0] }}</div>
                                </div>
                                <!-- <div class="form-group">
                                    <label for="field-payment_status">Payment Status</label>
                                    <b-form-select v-model="form.payment_status" id="payment_status">
                                        <b-form-select-option value="draft">Draft</b-form-select-option>
                                        <b-form-select-option value="pending">Pending</b-form-select-option>
                                        <b-form-select-option value="recieved">Recieved</b-form-select-option>
                                        <b-form-select-option value="cancel">Cancel</b-form-select-option>
                                    </b-form-select>
                                </div> -->
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input-group style-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-icon">
                                                <i class="fa fa-search"></i>
                                            </span>
                                        </div>
                                        <input type="search" class="form-control pl-0" placeholder="Scan / Enter Product Name" autofocus>
                                        <div class="input-group-append">
                                            <BrowseProduct @select="data => addCart(data)" type="purchase"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <table class="table table-vcenter" id="pembelian_table">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th width="36%">Product</th>
                                            <th width="14%">Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Discount</th>
                                            <th>Tax</th>
                                            <th>Net Price</th>
                                            <th>Subtotal</th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template v-if="lines.length">
                                            <tr v-for="(line, index) in lines" :key="index">
                                                <td class="px-0">
                                                    <a href="javascript:void(0)" @click="ModalUpdateProduct(line)" class="link-effect font-w700">{{ line.name }}
                                                        <span v-if="line.variant_name">, {{ line.variant_name }}</span>
                                                    </a>
                                                </td>
                                                <td>
                                                    <div class="input-group product-qty product-qty-sm">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-noborder btn-primary btn-sm m-0" type="button" @click="decrement(line.variant_id)">
                                                                <i class="fa fa-minus"></i>
                                                            </button>
                                                        </span>
                                                        <input type="text" v-model="line.qty" class="form-control input-number text-center form-control-sm">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-noborder btn-primary btn-sm m-0" type="button" @click="increment(line.variant_id)">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ currency(line.price) }}
                                                </td>
                                                <td>
                                                    {{ currency(line.discount_amount) }}
                                                </td>
                                                <td>
                                                    {{ currency(line.tax_amount) }}
                                                </td>
                                                <td>
                                                    {{ currency(line.price - line.discount_amount + line.tax_amount) }}
                                                </td>
                                                <td>
                                                    {{ currency(line.subtotal) }}
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-danger btn-noborder" @click="deleteCart(line.variant_id)" title="Delete">
                                                        <i class="si si-close"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5" class="font-w700 text-uppercase text-right">Total</td>
                                            <td colspan="2" class="font-w700 text-right">{{ currency(form.total) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="field-discount_type">Discount Type</label>
                                    <b-form-select v-model="form.discount_type" id="field-discount_type" name="discount_type">
                                        <b-form-select-option value="fixed">Fixed</b-form-select-option>
                                        <b-form-select-option value="percentage">Percentage</b-form-select-option>
                                    </b-form-select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="field-discount_amount">Discount Amount</label>
                                    <input type="number" min="0" max="100" class="form-control" v-model.number="form.discount_value" id="field-discount_amount" v-if="form.discount_type == 'percentage'">
                                    <CurrencyInput v-model="form.discount_amount" id="field-discount_amount" class="form-control" v-else/>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="mr-5">Discount</label>:
                                   <div class="font-w700"> (-) {{ currency(form.discount_amount) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="justify-content-between row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="field-discount_type">Tax</label>
                                    <tax-rate-select @done="(value) => form.tax = value" :data="(editMode) ? data.tax : null" :error="errors.tax_id"/>                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="mr-5">Tax</label>:
                                    <div class="font-w700">(+) {{ currency(form.tax_amount) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="field-note">Additional Notes</label>
                                    <textarea class="form-control" id="field-note" v-model="form.note"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-4 ml-auto">
                                <div class="form-group">
                                    <label class="mr-5">Grand Total</label>:
                                    <div class="font-w700">{{ currency(form.grand_total) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <b-modal id="product-info" ref="product-info" size="md" content-class="rounded" body-class="p-0" centered hide-footer hide-header>
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
                            <CurrencyInput v-model.lazy="detail.price" id="product-price" class="form-control"/>
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
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import SupplierSelect from '@/Components/Form/SupplierSelect.vue';
import TaxRateSelect from '@/Components/Form/TaxRateSelect.vue';
import flatPickr from 'vue-flatpickr-component';
import ProductQty from "@/components/Product/ProductQty.vue";
import CurrencyInput  from '@/components/Form/CurrencyInput.vue';
import BrowseProduct from '@/components/Product/ProductBrowseModal.vue';
import _ from 'lodash';
import { useVuelidate } from '@vuelidate/core'
import { required, minLength, helpers , between, email, decimal, integer, max,} from '@vuelidate/validators';

export default {
    setup () { 
        return { 
            v$: useVuelidate(),

        } 
    },
    components: {
        BaseLayout,
        SupplierSelect,
        flatPickr,
        CurrencyInput,
        ProductQty,
        BrowseProduct,
        TaxRateSelect,
    },
    data(){
        return {
            form : {
                id: null,
                ref : null,
                supplier_id : null,
                date : new Date(),
                discount_type : 'fixed',
                discount_amount : 0,
                discount_value : 0,
                status : 'draft',
                total : 0,
                payment_status : null,
                tax_id : null,
                tax_amount : 0,
                note : null,
                grand_total : 0,
                tax : null,
            },
            config: {
                altFormat: 'j F Y',
                altInput: true,
                dateFormat: 'Y-m-d',
                showMonths: 1,
            },
            lines : [],
            detail :{
                id : null,
                variant_id : null,
                name : null,
                price : null,
                qty : null,
                discount_type : null,
                discount_amount : 0,
                discount_value : 0,
                subtotal : null,
            },
            removed : [],
            title : 'Create New Purchase Order',
            
        }
    },
    props : {
        data : Object,
        errors : Object,
        editMode: Boolean,
    },
    validations () {
        return {
            detail : {
                price : {
                    required,
                },
            },
        }
    },
    mounted() {
        if(this.editMode){
            this.editModeActive();
        }
    },
    watch: {
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
                if(val.tax){
                    val.tax_id = val.tax.id;
                    val.tax_amount = (val.total - val.discount_amount) * val.tax.amount /100;
                }else{
                    val.tax_amount = 0;
                }
                // if(val.payment_received){
                // }


                val.grand_total = (val.total - val.discount_amount) + val.tax_amount;

                val.payment_change = val.payment_received - val.grand_total;
            }
        }
    },
    methods: {
        //----------- Calcul Total
        CaclulTotal() {
            this.form.total = 0;
            for (var i = 0; i < this.lines.length; i++) {
                // var tax = this.lines[i].taxes * this.lines[i].qty;

                if(this.lines[i].discount_amount){
                    this.lines[i].subtotal = this.lines[i].qty * (this.lines[i].price - this.lines[i].discount_amount);

                }else{
                    this.lines[i].subtotal = this.lines[i].qty * this.lines[i].price;
                }

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
                
                product.forEach((e,i) => {
                    if(this.lines.some(detail => detail.variant_id === e.variant_id)){
                        this.increment_qty_scanner(e.variant_id);
                    }else{
                        this.lines.push(e);
                    }
                });
            }else{
                // this.audio.play();
                this.lines.push(...product);
            }
            this.CaclulTotal();
            this.$forceUpdate();
        },

        //-----------------------------------Delete Detail Product ------------------------------\\
        deleteCart(variant_id) {
            for (var i = 0; i < this.lines.length; i++) {
                if (variant_id === this.lines[i].variant_id) {
                    console.log(this.lines[i].id);
                    if(this.lines[i].id){
                        this.removed.push(this.lines[i].id);
                    }
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
                    // if (this.lines[i].qty + 1 > this.details[i].current) {
                        // this.makeToast("warning", this.$t("LowStock"), this.$t("Warning"));
                    // } else {
                        this.lines[i].qty++;
                    // console.log('add');
                    // }
                }
            }
            // this.CaclulTotal();
            this.$forceUpdate();
        },
 
        //----------------------------------- Increment QTY ------------------------------\\
        increment(variant_id) {
            for (var i = 0; i < this.lines.length; i++) {
                if (this.lines[i].variant_id == variant_id) {
                    // if (this.lines[i].qty + 1 > this.lines[i].current) {
                    //     this.makeToast("warning", this.$t("LowStock"), this.$t("Warning"));
                    // } else {
                        this.lines[i].qty++;
                    // }
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
            // this.detail = detail;
            this.detail.product_id = detail.product_id;
            this.detail.variant_id = detail.variant_id;
            this.detail.name = detail.name;
            this.detail.price = detail.price;
            this.detail.qty = detail.qty;
            // this.product.fix_price = product.fix_price;
            // this.product.fix_stock = product.fix_stock;
            // this.product.current = product.current;
            // this.product.tax = product.tax;
            this.detail.discount_type = detail.discount_type;
            this.detail.discount_value = detail.discount_value;
            this.detail.discount_amount = detail.discount_amount;
            this.$bvModal.show("product-info");
        }, 

        updateProduct(){
            this.v$.detail.$validate()
            if (this.v$.detail.$error) {
                return
            }
            for (var i = 0; i < this.lines.length; i++) {
                if (this.lines[i].variant_id === this.detail.variant_id) {
                    // for (var k = 0; k < this.units.length; k++) {
                    //     if (this.units[k].id == this.detail.sale_unit_id) {
                    //         if (this.units[k].operator == "/") {
                    //             this.details[i].current =
                    //                 this.detail.fix_stock * this.units[k].operator_value;
                    //             this.details[i].unitSale = this.units[k].ShortName;
                    //         } else {
                    //             this.details[i].current = this.detail.fix_stock / this.units[k].operator_value;
                    //             this.details[i].unitSale = this.units[k].ShortName;
                    //         }
                    //     }
                    // }

                    if (this.lines[i].current < this.lines[i].quantity) {
                        this.lines[i].quantity = this.lines[i].current;
                    } else {
                        this.lines[i].quantity = 1;
                    }

                    this.lines[i].price = this.detail.price;
                    // this.lines[i].tax_percent = this.detail.tax_percent;
                    // this.lines[i].tax_method = this.detail.tax_method;
                    this.lines[i].discount_type = this.detail.discount_type;
                    this.lines[i].discount_amount = this.detail.discount_amount;
                    this.lines[i].discount_value = this.detail.discount_value;

                    if (this.lines[i].discount_type == "fixed") {
                        //Fixed
                        this.lines[i].discount_amount = this.lines[i].discount_amount;
                    } else {
                        //Percentage %
                        this.lines[i].discount_amount = (this.lines[i].price * this.lines[i].discount_value) / 100;
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

        submit: function(){
            let data = {};
            data = Object.assign(data, this.form)
            let lines = {
                lines : this.lines,
                removed : this.removed,
            }
            data = Object.assign(data, lines)

            let form = this.$inertia.form(data)
            let url = this.editMode ? this.route("admin.purchase.order.update") : this.route("admin.purchase.order.store");
            form.post(url, {
                preserveScroll: true,
                resetOnSuccess: false,
                onSuccess: () => {
                    return this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Purchase Order Added Successfully!`,
                        showCancelButton: true,
                        cancelButtonText : 'Cancel',
                        confirmButtonText: 'Add Another',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.$inertia.visit(this.route("admin.purchase.order.create"));
                        }
                    });
                },
            });
        },

        editModeActive(){
            if(this.editMode){
                this.title = 'Update Purchase Order';
                this.form.id = this.data.id;
                this.form.ref = this.data.ref;
                this.form.date = this.data.date;
                this.form.discount_type = this.data.discount_type;
                this.form.supplier_id = this.data.supplier_id;
                this.form.discount_amount = this.data.discount_amount;
                this.form.discount_value = this.data.discount_value;
                this.form.tax_id = this.data.tax_id ;
                this.form.tax_amount = this.data.tax_amount;
                this.form.total = this.data.total;
                this.form.grand_total = this.data.grand_total;

                if(this.data.line.length > 0){
                    this.data.line.forEach((value, index) => {
                        
                        let line = {
                            id : value.id,
                            product_id : value.product_id,
                            name : value.product.name,
                            variant_id : value.variant_id,
                            variant_name : '',
                            price : value.unit_price,
                            discount_type : value.discount_type,
                            discount_value : value.discount_value,
                            discount_amount : value.discount_amount,
                            qty : value.qty,
                            subtotal : value.sub_total,
                            tax_id : value.tax_id,
                            tax_amount : value.tax_amount,
                        }
                       this.lines.push(line);
                    });
                }
            }
        },
        confirmOrder(){

        }
    }
}
</script>
