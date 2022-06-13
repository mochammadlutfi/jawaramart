<template>
  <!-- Footer -->
    <footer id="page-footer">
    <div class="content py-20 font-size-sm clearfix">
        <div class="float-left">
            <a class="font-w600" :href="route('home')" target="_blank">{{ $page.props.settings.app_name }}</a> &copy; {{ year }}
        </div>
        <div class="float-right">
            <!-- Crafted with <i class="fa fa-heart text-pulse"></i> by <a class="font-w600" href="https://1.envato.market/ydb" target="_blank">pixelcave</a> -->
        </div>
    </div>
    </footer>
  <!-- END Footer -->
</template>

<script>
import moment from 'moment';
export default {
    name: 'BaseFooter',
    props: {
        classes: String
    },
    computed :{
        year(){
            moment().locale('id');
            return moment().format('YYYY');
        }
    }
}
</script>
