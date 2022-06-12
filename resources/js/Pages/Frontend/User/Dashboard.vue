<template>
    <UserLayout>
        <div class="content-heading pt-0 mb-3">
            Dashboard
        </div>
        <div class="row gutters-tiny">
            <!-- Orders -->
            <div class="col-md-6 col-xl-4" v-for="(d, i) in overview" :key="i">
                <a class="block block-rounded block-link-shadow block-bordered" :href="(d.url) ? d.url : 'javascript:void(0)'">
                    <div class="block-content block-content-full block-sticky-options">
                        <div class="block-options">
                            <div class="block-options-item" :class="d.color">
                                <i :class="d.icon"></i>
                            </div>
                        </div>
                        <div class="py-20 text-center">
                            <div class="font-size-h2 font-w700 mb-0" :class="d.color">
                                <template v-if="d.type != 'money' ">
                                    {{ d.data }}
                                </template>
                                <template v-else>
                                    {{ currency(d.data) }}
                                </template>
                            </div>
                            <div class="font-size-sm font-w700 text-uppercase text-muted">{{ d.title }}</div>
                        </div>
                    </div>
                </a>
            </div>
            <!-- END Orders -->
        </div>
    </UserLayout>
</template>

<script>
import UserLayout from "@/Layouts/Frontend/UserLayout";
export default {
    components: {
        UserLayout,
    },
    props : {
        overview : Array,
    },
    data(){
        return {
        }
    },
    mounted(){
    },
    methods : {
    }
 }

</script>
