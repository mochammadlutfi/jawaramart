<template>
    <BaseLayout>
        <section class="content content-full">
            <div class="row">
                <div class="col-xl-3">
                    <!-- <div class="side-filter">
                        <div class="block block-bordered block-shadow">
                            <div class="block-content">
                                Category Listing
                            </div>
                        </div>
                    </div> -->
                </div>
                <div class="col-xl-9">
                    <div class="col-6 col-lg-2 col-md-2 mb-15 px-5" v-for="(data, index) in products.data" :key="index">
                        <ProductItem :product="data" />
                    </div>
                </div>
            </div>
        </section>
    </BaseLayout>
</template>

<script>
import ProductItem from './ProductItem.vue';
import BaseLayout from "@/layouts/frontend/BaseLayout";
export default {
    components: {
        BaseLayout,
        ProductItem
    },
    props : {
        products : Object,
    }
 }

</script>
