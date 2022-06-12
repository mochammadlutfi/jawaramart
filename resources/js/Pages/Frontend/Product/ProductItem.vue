<template>
    <observer @on-change="onChange" class="product">
        <div class="product-content">
            <div class="product-img">
                <template v-if="loading">
                    <vue-skeleton-loader
                    type="rect"
                    :width="'100%'"
                    :height="'210px'"
                    animation="fade"
                    />
                </template>
                <template v-else>
                    <a :href="route('product.show', { 'product' : product.slug })">
                        <img :src="product.main_image" class="img-fluid"/>
                    </a>
                </template>
            </div>
            <div class="product-info">
                <div class="product-title"><a :href="route('product.show', { 'product' : product.slug })">{{ product.name }}</a></div>
                <div class="product-price">{{ currency(product.sell_price) }}</div>
            </div>
        </div>
    </observer>
</template>

<script>

import VueSkeletonLoader from 'skeleton-loader-vue';
import Observer from 'vue-intersection-observer'
export default {
    name : 'ProductItem',
    props : ['product'],
    components: {
        Observer,
        VueSkeletonLoader
    },
    data() {
        return{
            loading: false,
        }
    },
    methods: {
        onChange(entry, unobserve) {
            if (entry.isIntersecting) {
                unobserve();
            }

            this.loading = entry.isIntersecting ? false : true;
        }
    },
}
</script>
