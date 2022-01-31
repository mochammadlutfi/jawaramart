<template>
    <BaseLayout>
            <Slider/>
            <CategorySlide />
            <popular-product />
    </BaseLayout>
</template>

<script>
import BaseLayout from "@/layouts/frontend/BaseLayout";
import CategorySlide from './Product/CategorySlide.vue';
import PopularProduct from './Product/PopularProduct.vue';
import Slider from './Components/slider.vue';
export default {
    components: {
        BaseLayout,
        CategorySlide,
        PopularProduct,
        Slider,
    },
 }

</script>
