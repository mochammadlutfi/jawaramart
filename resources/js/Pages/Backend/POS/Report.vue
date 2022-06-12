<template>
    <BaseLayout title="List Sales">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Cash Register Closed
            </div>
            
            <div class="block block-rounded block-shadow-2 block-bordered mb-5">
                <div class="block-content">

                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<script>
export default {
    components: {
        BaseLayout,
    },
    data(){
        return {
        } 
    },
    props: {
        dataList: Object,
    },
    methods : {

    }
}
</script>
