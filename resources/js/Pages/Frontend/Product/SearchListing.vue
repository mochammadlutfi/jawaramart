<template>
    <BaseLayout>
        <section class="content content-full">
            <template v-if="products.data.length">
                <div class="content-heading">
                    <div class="dropdown float-right mr-5">
                        <span class="font-size-sm">Urutkan</span>
                        <b-dropdown id="orderBy" :text="sortByText" variant="btn btn-sm btn-secondary">
                            <b-dropdown-item @click="orderBy(0)">Terbaru</b-dropdown-item>
                            <b-dropdown-item @click="orderBy(1)">Harga Terendah</b-dropdown-item>
                            <b-dropdown-item @click="orderBy(2)">Harga Tertinggi</b-dropdown-item>
                        </b-dropdown>
                    </div>
                    <div>Menampilkan {{ products.total }} produk untuk "{{ search }}" <small>({{ products.current_page }} - {{ products.to }} dari {{ products.total }})</small></div>
                </div>
                <div class="row">
                    <div class="col-6 col-lg-2 col-md-2 mb-15 px-5" v-for="data in products.data" :key="data.id">
                        <ProductItem :product="data" />
                    </div>
                </div>
                <observer @on-change="onScroll">
                    <div class="test" :class="{'d-none': loading}"></div>
                </observer>
            </template>
            <template v-else>
                <h1>Opps Produk Yang Dicari Tidak Ditemukan</h1>
            </template>
        </section>
    </BaseLayout>
</template>

<script>
import ProductItem from './ProductItem.vue';
import BaseLayout from "@/layouts/frontend/BaseLayout";
import Observer from 'vue-intersection-observer';
import vSelect from 'vue-select';
export default {
    name : 'ProductSearch',
    components: {
        BaseLayout,
        Observer,
        vSelect,
        ProductItem
    },
    props : ['products'],
    data(){
        return {
            allData: this.products.data,
             loading : false,
             page : 1,
             sortBy : this.route().params.sort == undefined ? 0 : this.route().params.sort,
             search : this.route().params.q == undefined ? null : this.route().params.q,
        }
    },
    computed:{
        sortByText(){
            if(this.sortBy == 0){
                return 'Terbaru';
            }else if(this.sortBy == 1){
                return 'Harga Terendah';
            }else{
                return 'Harga Tertinggi';
            }
        }
    },
    mounted(){
        // var coba = ;
        // console.log('wq');
        // console.log(coba);
    },
    methods: { 
        onScroll(entry, obv) {
            if(entry.isIntersecting){
                this.loading = false;
                this.loadMorePosts();
            }else{
                this.loading = true;
            }
        },
        orderBy(value){
            this.sortBy = value;

            let params = {
                category_slug : this.category.slug,
                sort : this.sortBy,
                page : 1
            }
            
            this.$inertia.get(this.route('search', params), {
                preserveScroll : true,
            });
        },
        loadMorePosts() {
            if (this.products.next_page_url === null) {
                return
            }
            
            const params = {
                category_slug : this.category.slug,
                sort : this.sortBy,
                page : this.page
            }

            this.$inertia.get(this.route('search', params), {}, {
                preserveState: true,
                preserveScroll: true,
                only: ['products'],
                onSuccess: () => {
                    var result = Object.values(this.products.data);
                    this.allData = [
                    ...this.allData,
                    ...result,
                    ];
                    this.page = this.products.current_page;
                    // this.allData.push(...this.products.data);
                },
                onFinish: () =>{
                    this.loading = false;
                }
            })
        }
    } 
 }

</script>
