<template>
    <div>
        <button type="button" class="btn btn-secondary btn-sm" @click="openModal">
            <i class="fa fa-folder-open"></i>
        </button>
        <b-modal v-model="modalOpen" size="lg" content-class="rounded" no-close-on-backdrop body-class="p-0" hide-footer hide-header>
            <div class="block block-rounded block-transparent mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Browse Product</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" @click="close()">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
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
                                    <span v-if="products">{{ products.from }}-{{ products.to }}/{{ products.total }}</span>
                                </div>
                                <div class="pt-25 pl-0">
                                    <button type="button" class="btn btn-alt-secondary mx-1" @click.prevent="prevPage"
                                        :disabled="(products && products.prev_page_url) ? false : true">
                                    <i class="fa fa-chevron-left fa-fw"></i>
                                    </button>
                                    <button class="btn btn-alt-secondary mx-1" type="button" @click.prevent="nextPage"
                                        :disabled="(products &&  products.next_page_url) ? false : true">
                                    <i class="fa fa-chevron-right fa-fw"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block-content p-0">
                    <table class="table table-vcenter table-sm table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="2%">
                                    <b-form-checkbox id="selectAll" name="selectAll" v-model="selectAll" :indeterminate="indeterminate" @input.native="checkAll"></b-form-checkbox>
                                </th>
                                <th width="45px">Image</th>
                                <th>Name</th>
                                <th width="15%">Price</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-if="loading == true">
                            <tr v-if="loading == true">
                                <td colspan="5">
                                    <div class="text-center py-50">
                                        <div class="spinner-border text-primary wh-50" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </template>
                            <template v-for="data in products.data">
                                <tr :key="data.id" @click="selectRow(data, 0)" class="c-pointer" :class="{ opened: opened.includes(data.id) }">
                                    <td>
                                        <template v-if="data.variant.length > 1">
                                            <i class="fa fa-chevron-down fa-fw" v-if="opened.includes(data.id)"></i>
                                            <i class="fa fa-chevron-right fa-fw" v-else></i>
                                        </template>
                                        <template v-else>
                                            <b-form-checkbox :ref="'data-'+data.variant[0].id" name="data" v-model="checkedList" :value="data.variant[0].id" @input.native="handleClick(data, $event, 0)">
                                            </b-form-checkbox>
                                        </template>
                                    </td>
                                    <td> <img class="media-object" :src="data.main_image" style="max-width:45px"></td>
                                    <td>{{ data.name }}</td>
                                    <td>{{ currency(data.sell_price) }}</td>
                                    <td>{{ data.stock_count }}</td>
                                </tr>
                                <template v-if="data.variant.length > 1">
                                    <tr v-for="(v, i) in data.variant" :key="v.id" v-show="opened.includes(data.id)">
                                        <td>
                                            <b-form-checkbox :ref="'data-'+v.id" name="data" :value="data.variant[i].id" v-model="checkedList" @input.native="handleClick(data, $event, i)">
                                            </b-form-checkbox>
                                        </td>
                                        <td></td>
                                        <td>{{ v.variant }}</td>
                                        <td>{{ currency(v.sell_price) }}</td>
                                        <td>{{ data.stock_count }}</td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div class="block-content block-content-full text-right border-top" v-if="!loading">
                    <button type="button" class="btn btn-danger btn-noborder mr-1"  @click.prevent="close">
                        <i class="si si-close mr-1"></i>
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary btn-noborder mr-1" @click.prevent="submit">
                        <i class="si si-check mr-1"></i>
                        Select {{ selectedProduct.length }} Product
                    </button>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import axios from 'axios';
import vue from 'vue';

export default {
    name: 'ProductBrowseModal',
    components: {
    },
    data() {
        return {
            modalOpen: false,
            products : [],
            loading : true,
            selectAll: false,
            indeterminate: false,
            checkedList: [],
            selectedProduct : [],
  	        opened: [],
            search : "",
            page : 1,
        };
    },
    props : {
        type : String,
    },
    created(){
        this.fethData;
    },
    watch :{
        selectAll(newValue) {
            if (newValue.length === 0) {
                this.indeterminate = false
                this.selectAll = false
            } else if (newValue.length === this.checkedList.length) {
                this.indeterminate = false
                this.selectAll = true
            } else {
                this.indeterminate = true
                this.selectAll = false
            }
        }
    },
    methods: {
        nextPage: function() {
            if(this.page < this.products.total){
                this.page++;
                this.fethData();
            }
        },
        prevPage: function() {
            if(this.page > 1){
                this.page--;
                this.fethData();
            }
        },
        doSearch: _.throttle(function () {
            this.fethData();
        }, 200),

        openModal(){
            this.modalOpen = true;
            this.fethData();
        },

        async fethData(){
            const self = this;
            await axios.get(self.route("admin.product.data"),{
                params: {
                    page: this.page,
                    search : this.search
                }
            })
            .then(function (response) {
                var resp = response.data;
                self.products = resp.data;
                self.page = resp.data.current_page;
                self.loading = false;
            })
            .catch(function (error) {
                
            })
        },

        close(){
            this.modalOpen = false;
            this.products = [];
            this.selectedProduct = [];
            this.opened = [];
            this.checkedList = [];
            this.indeterminate = false;
            this.selectAll = false;
            this.loading = true;
            this.page = 1;
            this.search = "";
            this.$emit('close');
        },

        select(product){
            var data;
            if(product.variant.length > 1){
                
            }else{
                if(this.type == 'sale'){
                    if(product.stock_count > 1){
                        data = {
                            id : product.id,
                            name : product.name,
                            variant_id : product.variant[0].id,
                            variant_name : product.variant[0].name,
                            price : product.variant[0].sell_price,
                            net_price : product.variant[0].sell_price,
                            discount_type : 'fixed',
                            discount_value : 0,
                            discount_amount : 0,
                            qty : 1,
                            subtotal : 1 * product.variant[0].sell_price,
                            tax_id : null,
                            tax_amount : 0,
                            max_stock : product.stock_count
                        }
                        this.$emit('select', data)
                        this.modalOpen = false;
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
                }else{
                    data = {
                        id : product.id,
                        name : product.name,
                        variant_id : product.variant[0].id,
                        variant_name : product.variant[0].name,
                        price : product.variant[0].sell_price,
                        net_price : product.variant[0].sell_price,
                        discount_type : 'fixed',
                        discount_value : 0,
                        discount_amount : 0,
                        qty : 1,
                        subtotal : 1 * product.variant[0].sell_price,
                        tax_id : null,
                        tax_amount : 0,
                        max_stock : product.stock_count
                    }
                    this.$emit('select', data)
                }
            }
        },

        selectRow(product, index) {
            if(product.variant.length > 1){
                const index = this.opened.indexOf(product.id);
                if (index > -1) {
                    this.opened.splice(index, 1)
                } else {
                    this.opened.push(product.id)
                }
            }
        },

        checkAll(e) {
            if(e.target.checked){
                this.products.data.forEach((product, i) => {
                    if(product.variant.length > 1){
                        product.variant.forEach((v, e) => {
                            if(this.type == 'sales'){
                                if(product.stock_count > 1){
                                    this.checkedList.push(v.id);
                                    this.opened.push(v.product_id);
                                    this.addProduct(product, e);
                                }else{
                                    this.$swal.fire({
                                        icon: 'error',
                                        title: 'Oops!',
                                        text: `Some products can't be selected due to out of stock!`,
                                        showConfirmButton : false,
                                        showCancelButton: false,
                                        timer : 1500,
                                    });
                                }
                            }else{
                                this.checkedList.push(v.id);
                                this.opened.push(v.product_id);
                                this.addProduct(product, e);
                            }
                        });
                    }else{
                        if(this.type == 'sales'){
                            if(product.stock_count > 1){
                                this.checkedList.push(product.variant[0].id);
                                this.addProduct(product, 0);
                                this.opened.push(product.id);
                            }else{
                                this.$swal.fire({
                                    icon: 'error',
                                    title: 'Oops!',
                                    text: `Some products can't be selected due to out of stock!`,
                                    showConfirmButton : false,
                                    showCancelButton: false,
                                    timer : 1500,
                                });
                            }
                        }else{
                            this.checkedList.push(product.variant[0].id);
                            this.addProduct(product, 0);
                            this.opened.push(product.id);
                        }
                    }
                });
            }else{
                this.checkedList = [];
                this.selectedProduct = [];
                this.opened = [];
            }
        },
        
        addProduct(product, index){
            let variant_name = product.variant[index].variant;
            if(product.variant[index].variant2){
                variant_name += '-';
                variant_name += product.variant[index].variant2;
            }

            var data = {
                id : product.id,
                name : product.name,
                variant_id : product.variant[index].id,
                variant_name : variant_name,
                price : product.variant[index].sell_price,
                net_price : product.variant[index].sell_price,
                discount_type : 'fixed',
                discount_value : 0,
                discount_amount : 0,
                qty : 1,
                subtotal : 1 * product.variant[index].sell_price,
                tax_id : null,
                tax_amount : 0,
                max_stock : product.stock_count
            }
            this.selectedProduct.push(data);
        },

        handleClick(product, e, i) {
            if(e.target.checked){
                if(this.type == 'sales'){
                    if(product.stock_count > 1){
                        this.addProduct(product, i);
                    }else{
                        e.target.checked = false;
                        this.$swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: `Product stock is empty!`,
                            showConfirmButton : false,
                            showCancelButton: false,
                            timer : 1500,
                        });
                    }
                }else{
                    this.addProduct(product, i);
                }
            }else{
                this.selectedProduct.forEach((e, f) => {
                    if(product.id == e.id && product.variant[i].id == e.variant_id){
                        this.selectedProduct.splice(this.selectedProduct.indexOf(f), 1);
                    }
                });
            }
        },

        submit(){
            this.$emit('select', this.selectedProduct)
            this.close();
        }
    },
}

</script>