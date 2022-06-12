<template>
    <div>
        <div class="block block-rounded block-shadow block-bordered d-md-block d-none mb-10">
            <div class="block-content p-2">
                <div class="row justify-content-between">
                    <div class="col-lg-4">
                        <div class="has-search">
                            <i class="fa fa-search"></i>
                            <input type="search" class="form-control" id="pos-product-search" ref="product-search" v-model="search" autocomplete="off" @keyup="doSearch" @change="doSearch" autofocus>
                        </div>
                    </div>
                    <div class="col-lg col-end">
                        <div class="d-flex float-right">
                            <div class="my-auto px-3">
                                <span>{{ (products) ? products.from : 0 }}-{{ (products) ? products.to : 0 }}/{{ (products) ? products.total : 0 }}</span>
                            </div>
                            <div class="pt-25 pl-0">
                                <button type="button" class="btn btn-alt-secondary mx-1" @click.prevent="prevPage" :disabled="checkPaginate('prev')">
                                <i class="fa fa-chevron-left fa-fw"></i>
                                </button>
                                <button class="btn btn-alt-secondary mx-1" type="button" @click.prevent="nextPage" :disabled="checkPaginate('next')">
                                <i class="fa fa-chevron-right fa-fw"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="block block-transparent">
            <div class="block-content py-0 px-0">
                <div class="row gutters-tiny" v-if="products">
                    <div class="col-4 col-lg-3 col-md-2 mb-15 px-5" v-for="(data, index) in products.data" :key="index">
                        <div class="product" @click.prevent="select(data)">
                            <div class="product-content">
                                <div class="product-img">
                                    <img :src="data.main_image" class="img-fluid"/>
                                </div>
                                <div class="product-info">
                                    <div class="product-title">{{ data.name }}</div>
                                    <div class="product-price">{{ currency(data.sell_price) }}</div>
                                    <div class="product-stock">Stock : {{ data.stock_count }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import _ from 'lodash';
export default {
    name : 'POS-Product',
    components: {
    },
    data() {
        return {
            products : null,
            selected : null,
            search: null,
            page: 1,
        }
    },
    created(){
        // Add barcode scan listener and pass the callback function
        this.$barcodeScanner.init(this.onBarcodeScanned)
        this.data();
    },
    methods : {
        checkPaginate(type){
            const vm = this;
            if(vm.products){
                if(type == 'next'){
                    return (vm.products.next_page_url) ? false : true;
                }else{
                    return (vm.products.prev_page_url) ? false : true;
                }
            }else{
                return true;
            }
        },
        nextPage: function() {
            if(this.page < this.products.total){
                this.page++;
                this.data();
            }
        },
        prevPage: function() {
            if(this.page > 1){
                this.page--;
                this.data();
            }
        },
        async data(){
            const self = this;
            await axios.get(this.route("admin.product.data"),{
                params: {
                    page: this.page,
                    search : this.search
                }
            })
            .then(function (response) {
                const resp = response.data;
                self.products = resp.data;
                if(self.products.data.length == 1){
                    // alert('sad');
                    // console.log(self.products.data[0]);
                    // self.test(resp.data.data[0]);
                    self.select(resp.data.data[0]);
                    self.search = null;
                }
                self.page = resp.data.current_page;
            })
            .catch(function (error) {
                
            })
        },
        test(){
            alert('sad')
        },
        select(product){
            var data;
            if(product.variant.length > 1){
                
            }else{
                if(product.stock_count > 0){
                    data = {
                        id : product.id,
                        name : product.name,
                        variant_id : product.variant[0].id,
                        variant_name : product.variant[0].name,
                        price : product.variant[0].sell_price,
                        net_price : product.variant[0].sell_price,
                        discount_type : 'fixed',
                        discount_value : null,
                        discount_amount : null,
                        qty : 1,
                        subtotal : 1 * product.variant[0].sell_price,
                        tax : null,
                        max_stock : product.stock_count
                    }
                    this.$emit('select', data)
                }else{
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: `Product stock is empty!`,
                        showConfirmButton : false,
                        showCancelButton: false,
                        timer : 1500,
                    });
                }
            }
        },
        doSearch : _.throttle(function(){
            this.data();
        }, 1500),
        
        // Create callback function to receive barcode when the scanner is already done
        onBarcodeScanned (barcode) {
            // console.log(barcode)
            this.search = barcode;
            this.doSearch();
            // do something...
        },
        // Reset to the last barcode before hitting enter (whatever anything in the input box)
        resetBarcode () {
            let barcode = this.$barcodeScanner.getPreviousCode()
            // do something...
        }
    },
    destroyed () {
      // Remove listener when component is destroyed
      this.$barcodeScanner.destroy()
    },
}
</script>
