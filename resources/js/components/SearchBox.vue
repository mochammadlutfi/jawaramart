<template>
    <div class="searchbox">
        <div class="searchbox_main">
            <form @submit.prevent="doSearch">
                <i class="fa fa-search mx-5" v-if="$root.window.mobile"></i>
                <input type="text" name="search" v-model="terms" placeholder="Cari produk" autocomplete="off">
                <div class="searchbox_action b-flex b-ai-center b-jc-end">
                    <i class="fa fa-times-circle clear" v-if="terms"  @click="terms = null"></i>
                    <button class="btn searchbox_search" type="submit" v-if="!$root.window.mobile" :disabled="terms.length < 3 ">
                    <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="searchbox__autocomplete">
            <ul class="p-0" id="search_items" style="display: none;">
                <li class="search_item" id="search_empty_list"></li>
                <li class="search_item" id="search_history"></li>
                <li class="search_item" id="tag_search">
                    <div class="tag_box">
                        <a href="javascript:void(0)" class="search_title ">popular Suggestions</a>
                    </div>
                    <div class="tags_list d-flex flex-column seach_list_padding"><a class="store_link" data-type="tag"
                            href="https://amazcart.infixdev.com/category/helmet?item=search&amp;category_id=0"><span
                                class="highlight">helmet</span></a><a class="store_link" data-type="tag"
                            href="https://amazcart.infixdev.com/category/exclusive helmet?item=search&amp;category_id=0">exclusive
                            <span class="highlight">helmet</span></a><a class="store_link" data-type="tag"
                            href="https://amazcart.infixdev.com/category/half face helmet?item=search&amp;category_id=0">half
                            face <span class="highlight">helmet</span></a><a class="store_link" data-type="tag"
                            href="https://amazcart.infixdev.com/category/helmet for bike?item=search&amp;category_id=0"><span
                                class="highlight">helmet</span> for bike</a><a class="store_link" data-type="tag"
                            href="https://amazcart.infixdev.com/category/bicycle helmet?item=search&amp;category_id=0">bicycle
                            <span class="highlight">helmet</span></a><a class="store_link" data-type="tag"
                            href="https://amazcart.infixdev.com/category/mt helmet?item=search&amp;category_id=0">mt
                            <span class="highlight">helmet</span></a></div>
                </li>
                <li class="search_item" id="category_search">
                    <div class="tag_box">
                        <a href="javascript:void(0)" class="search_title">Category Suggestions</a>
                    </div>
                    <div class="search_category_list d-flex  flex-column seach_list_padding"><a class="store_link"
                            data-type="category"
                            href="https://amazcart.infixdev.com/category/helmet?item=category"><span
                                class="highlight">Helmet</span></a></div>
                </li>
                <li class="search_item" id="product_search">
                    <div class="tag_box">
                        <a href="javascript:void(0)" class="search_title">Products</a>
                    </div>
                    <div class="Products_list d-flex  flex-column seach_list_padding" id="search_product_list">
                        <a data-type="product"
                            href="https://amazcart.infixdev.com/product/mt-thunder-design-new-design-1"
                            class="store_link product_search_single d-flex align-items-center mb_10 gap_10">
                            <div class="product_info d-flex align-items-center flex-fill gap_10">
                                <div class="thumb">
                                    <img src="https://amazcart.infixdev.com/public/uploads/images/03-10-2021/6159b82b44337.png"
                                        alt="">
                                </div>
                                <div class="product_info_text">
                                    <h4 class="m-0 search_product_name">MT Thunder 3 <span
                                            class="highlight">helmet</span></h4>
                                    <div class="prise_tag d-flex align-items-center gap_10">

                                        <span class="prev_prise ">$ 120.00</span>
                                        <span class="current_prise">$ 115.00</span>

                                    </div>
                                </div>
                            </div>
                            <div class="product_available">In Stock</div>
                        </a>

                        <a data-type="product" href="https://amazcart.infixdev.com/product/half-face-strong-helmet-1-1"
                            class="store_link product_search_single d-flex align-items-center mb_10 gap_10">
                            <div class="product_info d-flex align-items-center flex-fill gap_10">
                                <div class="thumb">
                                    <img src="https://amazcart.infixdev.com/public/uploads/images/23-11-2021/619ce35c01901.png"
                                        alt="">
                                </div>
                                <div class="product_info_text">
                                    <h4 class="m-0 search_product_name">Half face strong <span
                                            class="highlight">helmet</span> 1</h4>
                                    <div class="prise_tag d-flex align-items-center gap_10">

                                        <span class="prev_prise ">$ 55.00</span>
                                        <span class="current_prise">$ 49.50</span>

                                    </div>
                                </div>
                            </div>
                            <div class="product_available">In Stock</div>
                        </a>

                        <a data-type="product" href="https://amazcart.infixdev.com/product/helmet-for-bicycle-1"
                            class="store_link product_search_single d-flex align-items-center mb_10 gap_10">
                            <div class="product_info d-flex align-items-center flex-fill gap_10">
                                <div class="thumb">
                                    <img src="https://amazcart.infixdev.com/public/uploads/images/24-11-2021/619e04c96b12b.png"
                                        alt="">
                                </div>
                                <div class="product_info_text">
                                    <h4 class="m-0 search_product_name"><span class="highlight">Helmet</span> for
                                        bicycle</h4>
                                    <div class="prise_tag d-flex align-items-center gap_10">
                                        <span class="current_prise">$ 25.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="product_available">In Stock</div>
                        </a>
                    </div>
                </li>
                <li class="search_item" id="seller_search"></li>
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
    padding: 4px;
    border-radius: 4px;
    -webkit-transition: width 2s ease-in-out;
    transition: width 2s ease-in-out;
    border: 2px solid #3f9ce8;
}

.searchbox_main input {
    background: #ffffff;
    height: 30px;
    padding-left: 5px;
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
    width: 40px;
    height: 32px;
    padding: 8px;
    background-color: #3f9ce8;
    border-radius: 4px;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
}

.searchbox_main .searchbox_action .searchbox_search i{
    line-height: 15px;
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

export default {
  components: {
    Head,
  },
  data(){
      return{
        terms : null,
      }
  },
  props: {
    title: String,
  },

  methods :{
      doSearch(e){
          alert(this.terms);
      }
  }
}
</script>