<template>
    <BaseLayout title="Product List">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Products
                <div class="float-right">
                    <button v-if="selected.length > 0" type="button" class="btn btn-sm btn-danger" >
                        <i class="si si-trash"></i>
                        Delete {{ selected.length }} Selected
                    </button>
                    <Link class="btn btn-sm btn-secondary" :href="route('admin.product.create')">
                        <i class="si si-plus"></i>
                        Create
                    </Link>
                </div>
            </div>
            <div class="block block-rounded block-shadow block-bordered d-md-block d-none mb-10">
                <div class="block-content p-2">
                    <div class="row justify-content-between">
                        <div class="col-4">
                            <div class="has-search">
                                <i class="fa fa-search"></i>
                                <input type="search" class="form-control" id="search-data-list" v-model="search" @keyup="doSearch()" @change="doSearch()">
                            </div>
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4">
                            <div class="d-flex float-right">
                                <div class="my-auto px-3">
                                    <span>{{ dataList.from }}-{{ dataList.to }}/{{ dataList.total }}</span>
                                </div>
                                <div class="pt-25 pl-0">
                                    <Link :href="dataList.prev_page_url ? dataList.prev_page_url : '#'" as="button" class="btn btn-alt-secondary mx-1" type="button"
                                    :disabled="dataList.prev_page_url ? false : true">
                                        <i class="fa fa-chevron-left fa-fw"></i>
                                    </Link>
                                    <Link :href="dataList.next_page_url ? dataList.next_page_url : '#'" as="button" class="btn btn-alt-secondary mx-1" type="button"
                                    :disabled="dataList.next_page_url ? false : true">
                                        <i class="fa fa-chevron-right fa-fw"></i>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block block-rounded block-shadow-2 block-bordered mb-5">
                <div class="block-content px-0 py-0">
                    <table class="table table-striped table-vcenter table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="2%">
                                    <b-form-checkbox id="selectAll" name="selectAll" v-model="selectAll"></b-form-checkbox>
                                </th>
                                <th width="45px">Image</th>
                                <th>Name</th>
                                <th width="15%">Sell Price</th>
                                <th>Total Stock</th>
                                <th width="10%"></th>
                            </tr>
                        </thead>
                        <tbody v-if="loading">
                            <tr>
                                <td colspan="5">
                                    <div class="text-center py-50">
                                        <div class="spinner-border text-primary wh-50" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <template v-if="Object.values(dataList.data).length">
                                <tr v-for="data in dataList.data" :key="data.id">
                                    <td>
                                        <b-form-checkbox
                                            :id="'data-'+data.id"
                                            v-model="selected"
                                            :name="'data-'+data.id"
                                            :value="data.id"
                                            >
                                        </b-form-checkbox>
                                    </td> 
                                    <td> <img class="media-object" :src="data.main_image" style="max-width:45px"></td>
                                    <td>{{ data.name }}</td>
                                    <td>{{ currency(data.sell_price) }}</td>
                                    <td>{{ data.total_stock }}</td>
                                    <th>
                                        <b-dropdown text="Action" class="m-md-2"  size="sm">
                                            <Link :href="route('admin.product.show', {id : data.id})" as="button" class="dropdown-item" type="button">
                                                Detail
                                            </Link>
                                            <Link :href="route('admin.product.edit', {id : data.id})" as="button" class="dropdown-item" type="button">
                                                Edit
                                            </Link>
                                        </b-dropdown>
                                    </th>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-if="!Object.values(dataList.data).length">
                                    <td>Data Kosong</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </BaseLayout>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import moment from 'moment';
import _ from 'lodash';
export default {
    components: {
        BaseLayout,
        Link,
    },
    data(){
        return {
            loading : false,
            selected: [],
            selectAll: false,
            search : this.route().params.search == '' ? '' : this.route().params.search
        } 
    },
    mounted() {
        // this.$inertia.on('start', (event) => {
        //     console.log('asd');
        // })
    },
    watch: {
        search: function () {
            this.doSearch() 
        },
        selectAll: function(val){
			this.selected = [];
            if(val){
                this.dataList.data.forEach((value, index) => {
                    this.selected.push(value.id)
                });
            }
        }
    },
    props: {
        dataList: Object,
    },
    methods :{
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MMM YYYY')
            }
        },
        doSearch : _.throttle(function(){
            this.$inertia.get(this.route('admin.product.index', { search : this.search }), {
                preserveScroll : true,
            })
        }, 200),
    }
}
</script>