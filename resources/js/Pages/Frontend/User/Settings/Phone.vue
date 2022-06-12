<template>
    <UserLayout title="Settings">
        <div>
            <div class="content-heading pt-3 mb-3">
                <i class="fi-rs-home-location mr-1"></i> Settings
            </div>
            <div class="block block-rounded block-shadow block-bordered">
                
            </div>
        </div>
    </UserLayout>
</template>
<style lang="scss">

</style>
<script>
import UserLayout from "@/Layouts/Frontend/UserLayout";
import _ from 'lodash';
import moment from 'moment';
export default {
    components: {
        UserLayout,
    },
    props : ['dataList'],
    data(){
        return {
            title : 'Tambah Alamat Baru',
        }
    },
    watch :{
    },
    methods :{
        onSearch(search, loading) {
            if(search.length) {
                loading(true);
                this.search(loading, search, this);
            }
        },
        format_date(value){
            if (value) {
                moment.locale('id');
                return moment(String(value)).format('DD MMMM YYYY h:mm')
            }
        },
        timeout(value){
            moment.locale('id');
            var created  = moment(String(value));
            return moment(created).format('DD MMM YYYY h:mm');
        },
    }
 }

</script>
