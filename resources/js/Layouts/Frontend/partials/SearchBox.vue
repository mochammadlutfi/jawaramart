<template>
    <div class="searchbox">
            <form @submit.prevent="doSearch">
            <div class="searchbox_main">
                    <i class="fa fa-search mx-5" v-if="$root.window.mobile"></i>
                    <input type="text" name="search" v-model="terms" placeholder="Cari produk" autocomplete="off" @focus="isOpen = true" @keyup="autocomplete">
                    <div class="searchbox_action b-flex b-ai-center b-jc-end">
                        <i class="fa fa-times-circle clear" v-if="terms"  @click="terms = null"></i>
                        <button class="btn searchbox_search" type="submit" v-if="!$root.window.mobile">
                        <i class="fa fa-search"></i>
                        </button>
                    </div>
            </div>
        </form>
        <div class="live-search">
            <ul class="p-0" id="search_items" :class="{ 'd-block' : isOpen }" @mouseover="isOpen = true" @mouseout="isOpen = false">
                <!-- <li class="search_item" id="search_empty_list"></li>
                <li class="search_item" id="search_history"></li> -->
                <!-- <li class="search_item" id="category_search" v-if="categories.length">
                    <div class="tag_box">
                        <a href="javascript:void(0)" class="search_title">Saran Kategori</a>
                    </div>
                    <div class="search_category_list d-flex  flex-column seach_list_padding">
                        <a class="store_link" data-type="category" href="https://amazcart.infixdev.com/category/helmet?item=category">
                            Helmet
                        </a>
                    </div>
                </li> -->
                <li class="search_item" v-if="products.length">
                    <div class="tag_box">
                        <a href="javascript:void(0)" class="search_title">Produk</a>
                    </div>
                    <div class="Products_list d-flex  flex-column seach_list_padding" id="search_product_list">
                        <a data-type="product" :href="route('product.show', { 'product' : p.slug })" class="product_search_single d-flex align-items-center mb_10" v-for="(p, i) in products" :key="i">
                            <div class="product_info d-flex align-items-center flex-fill gap_10">
                                <div class="thumb">
                                    <img :src="p.main_image" alt="">
                                </div>
                                <div class="product_info_text">
                                    <h4 class="m-0 search_product_name">
                                        {{ p.name }}
                                    </h4>
                                    <div class="prise_tag d-flex align-items-center gap_10">
                                        <!-- <span class="prev_prise ">{{ currency(p.sell_price) }}</span> -->
                                        <span class="current_prise">{{ currency(p.sell_price) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="product_available">In Stock</div>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
<style>
.searchbox{
    position: relative;
    width: 100%;
    margin-left: 10px;
}

.searchbox_main{
    background: #ffffff;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: justify;
    -ms-flex-pack: justify;
    justify-content: space-between;
    width: 100%;
    border-radius: 4px;
    -webkit-transition: width 2s ease-in-out;
    transition: width 2s ease-in-out;
    border: 1.5px solid #d0d2d5
}

.searchbox_main input {
    background: #ffffff;
    height: 30px;
    padding-left: 12px;
    border: none;
    font-size: 14px;
    width: 100%;
}

.searchbox_main .searchbox_action {
    width: 80px;
    display: flex;
    -webkit-box-pack: end;
    justify-content: end;
    -webkit-box-align: center;
    align-items: center;
}

.searchbox_main .searchbox_action .clear {
    font-size: 20px;
    margin-right: 4px;
    opacity: .4;
    cursor: pointer;
}

.searchbox_main .searchbox_action .searchbox_search {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    color: #f1f1f1;
    font-size: 14px;
    /* width: 40px;
    height: 32px; */
    /* padding: 8px; */
    margin: 3px;
    padding: 6px 10px;
    background-color: #3f9ce8;
    border-radius: 4px;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
}

.searchbox_main .searchbox_action .searchbox_search i{
    line-height: 15px;
}

.live-search ul {
    border: 1px solid #e4e7e9!important;
    display: none;
    border-top: 0!important;
    list-style: none;
    margin: 0;
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    z-index: 123;
    background: #fff;
}

.live-search li {
    border-right: 0!important;
    border-left: 0!important;
}
.live-search ul li:first-child {
    border-top: 0!important;
}
.live-search ul li:last-child {
    border-bottom: 0!important;
}
.live-search ul {
    border: 1px solid #e4e7e9!important;
    display: none;
    border-top: 0!important;
}

.live-search ul {
    list-style: none;
    margin: 0;
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    z-index: 123;
    background: #fff;
}
.live-search ul .search_item .search_title {
    display: flex;
    justify-content: flex-end;
    background: #f4f7f9;
    padding: 8px 10px;
    font-size: 12px;
    text-transform: uppercase;
    color: var(--text_color);
}

.live-search .tags_list a{
    font-size: 14px;
    font-weight: 400;
    color: var(--text_color);
    line-height: 2;
}
.live-search .tags_list a:hover{
    color: var(--base_color);
}
.live-search .search_category_list a{
    font-size: 14px;
    font-weight: 400;
    color: var(--base_color);
    line-height: 2
}
.seach_list_padding{
    padding: 10px 22px;
}
.live-search .Products_list a{
    font-size: 16px;
    font-weight: 400;
    color: var(--base_color);
    margin: 5px 0px;
}
.live-search .Products_list .product_info_text h4{
    font-size: 14px;
    font-weight: 400;
}
.live-search .Products_list .product_info_text .prise_tag{
    font-size: 14px;
    font-weight: 400;
}
.live-search .Products_list .product_info_text .prev_prise{
    font-size: 14px;
    font-weight: 400;
    text-decoration: line-through;
    color: #222222;
}
.live-search .Products_list .product_info_text .current_prise{
    font-size: 16px;
    font-weight: 500;
    color: var(--base_color);
}
.product_available{
    background: green;
    font-size: 12px;
    font-weight: 400;
    color: #fff;
    border-radius: 3px;
    display: inline-block;
    padding: 2px 8px;
}
.product_stockout{
    background: var(--base_color);;
    font-size: 12px;
    font-weight: 400;
    color: #fff;
    border-radius: 3px;
    display: inline-block;
    padding: 2px 8px;
}
.live-search .product_info_text h4{
    font-size: 14px;
    font-weight: 400
}
.live-search .product_info_text p{
    font-size: 14px;
    font-weight: 400
}
.live-search .thumb {
    width: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 55px;
}

.live-search .thumb img {
    max-width: 100%;
    max-height: 55px;
}
.highlight{
    font-weight: 500;
}
.search_empty_list_div {
    padding: 20px;
    margin: 0 auto;
    text-align: center;
}
.search_empty_list_div p {
    font-size: 16px;
    font-weight: 400;
}
.search_empty_list_div p strong{
    font-weight: 500;
}

@media screen and (max-width: 768px){
    .searchbox__main{
        padding: 8px 4px 8px 8px;
        background: #f1f1f1;
    }
}

@media screen and (max-width: 1023px){
    .searchbox_main{
        height: 38px;
    }

    .searchbox_main input{
        width: 100%;
    }
}
</style>
<script>
import { Head } from '@inertiajs/inertia-vue'
import _ from 'lodash';
import axios from 'axios'

export default {
  components: {
    Head,
  },
  data(){
      return{
        terms : this.route().params.q == undefined ? null : this.route().params.q,
        isOpen: false,
        products: [],
      }
  },
  watch:{
    // terms(value){
    //     if(value.length > 3){
    //         this.doSearch();
    //     }
    // }  
  },
  props: {
    title: String,
  },
  methods :{
        doSearch(e){
            if(this.terms.length > 3){
                let form = this.$inertia.form({
                    'q' : this.terms
                });
                let url = this.route("search");
                form.get(url, {
                    onError:(error) => {
                        this.$swal.close();
                    },
                });
            }
        },
        autocomplete : _.throttle(function(){
            const self = this;
            if(this.terms.length > 3){
                this.isOpen = true;
                axios.post(this.route("search.autocomplete"),{
                    search : this.terms
                })
                .then(function (response) {
                    // console.log(response);
                    const res = response.data;
                    // if(res.fail == false){
                        self.products = res.products;
                        console.log(res);
                    // }
                })
                .catch(function (error) {
                    
                })
            }
        }, 1000),
  }
}
</script>