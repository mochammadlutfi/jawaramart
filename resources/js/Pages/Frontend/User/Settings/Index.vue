<template>
    <UserLayout title="Settings">
        <div>
            <div class="content-heading pt-3 mb-3">
                <i class="fi-rs-home-location mr-1"></i> Settings
            </div>
            <div class="block block-rounded block-shadow block-bordered">
                <a :href="data.route" class="block-content settings-row" v-for="(data, i) in dataList" :key="i">
                    <div class="settings-icon">
                        <img :src="data.icon_src" alt="profile" v-if="data.icon_type == 'image'">
                    </div>
                    <div class="settings-desc text-mute">
                        <div class="settings-title text-black">{{ data.title }}</div>
                        <span class="text-muted">{{ data.sub_title }}</span>
                    </div>
                </a>
            </div>
        </div>
    </UserLayout>
</template>
<style lang="scss">
.settings-row{
    display: flex;
    flex: 1 1 0%;
    flex-direction: row;
    padding: 16px 20px;
    border-bottom: 1px solid #e4e7ed;

    &:last-child { border-bottom: none;  }

    .settings-icon {
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        margin-right: 16px;

        img{
            filter: invert(0%) sepia(6%) saturate(26%) hue-rotate(336deg) brightness(103%) contrast(100%);
            width: 24px;
            max-width: 24px;
            height: 24px;
        }
    }

    .settings-desc{

        .settings-title{
            -webkit-font-smoothing: antialiased;
            font-size: 16px;
            line-height: 24px;
            font-weight: 600;
        }
    }
}
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
