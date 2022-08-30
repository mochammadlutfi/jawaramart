<template>
    <div>
        <div class="block block-rounded block-shadow-2 block-bordered mb-5">
            <div class="block-content p-2">
                <div class="row justify-content-between">
                    <div class="col-sm-12 col-md-6">
                        <div class="dataTables_length">
                            <label>
                                <select aria-controls="DataTables_Table_4" class="form-control" v-model="currentLimit" @change="doSearch">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="has-search">
                            <i class="fa fa-search"></i>
                            <input type="search" class="form-control" id="search-data-list" v-model="search" @input="doSearch" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            <div class="block-content px-0 py-0">
                <table class="table table-striped table-vcenter table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="2%" v-if="checkbox">
                                <slot name="rowCheck"></slot>
                            </th>
                            <table-head v-for="(th, thi) in columns" :key="thi" :name="th.field" :width="th.width">
                                {{ th.name }}
                            </table-head>
                            <th width="13%"></th>
                        </tr>
                    </thead>
                    <tbody v-if="loading">
                        <tr>
                            <td :colspan="columns.length + 2">
                                <div class="text-center py-50">
                                    <div class="spinner-border text-primary wh-50" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <template v-if="values.data.length">
                            <slot name="body" :values="values.data"></slot>
                        </template>
                        <template v-else>
                            <tr>
                                <td :colspan="columns.length + 2">
                                    <div class="text-center">
                                        <img class="img-fluid" :src="asset('images/not_found.png')">
                                        <h3 class="font-size-24 font-w600 mt-3">Data Not Found</h3>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="block-content p-2">
                <div class="row">
                    <div class="col-sm-12 col-md-5">
                        <div class="py-2" id="table-info" role="status" aria-live="polite">
                            Showing {{ (values.from) ? values.from : 0 }} to {{ (values.to) ? values.to : 0 }} of {{ values.total }} entries
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-7">
                        <div class="float-right">
                            <ul class="pagination mb-0">
                                <li class="page-item" :class="{'active': link.active, 'disabled': link.url == null }" v-for="(link, p) in values.links" :key="p">
                                    <a class="page-link" :href="get_link(link.url)" v-html="link.label"></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import TableHead from "./TableHead.vue";
import _ from 'lodash';
export default {
    name : 'base-table',
    components : {
        TableHead
    },
    props : {
        columns : Array,
        values : Object,
        checkbox : Boolean,
        filter : Object,
        defaultSort : String,
        params : Object,
        limit: {
            type: Number,
            default: 25
        }
    },
    data(){
        return {
            loading : false,
            selected: [],
            search : this.route().params.search == undefined ? '' : this.route().params.search,
            currentSort: this.route().params.sort == undefined ? this.defaultSort : this.route().params.sort,
            currentSortDir: this.route().params.sortDir == undefined ? 'asc' : this.route().params.sortDir,
            currentPage: this.route().params.page == undefined ? 1 : this.route().params.page,
            currentLimit: this.route().params.limit == undefined ? this.limit : this.route().params.limit,
        }
    },
    provide: function() {
        return {
            getCurrentSort: this.currentSort,
            sortBy: this.sortBy,
            getSortIcon : this.sortIcon,
        };
    },
    computed: {
        sortIcon: function() {
            if (this.currentSortDir == "asc") {
                return '<i class="fa fa-sort-amount-up-alt"></i>';
            } else {
                return '<i class="fa fa-sort-amount-down-alt"></i>';
            }
        },
    },
    watch : {
        filter: {
            handler: function(val, oldVal) {
                this.fetchData();
            },
            deep: true,
        },
    },
    methods :{
        sortBy:function(s) {
            if(s === this.currentSort) {
                this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' :'asc';
            }
            this.loading = true;
            this.currentSort = s;
            this.fetchData();
        },
        nextPage: function() {
            if(this.currentPage < this.values.total){
                this.currentPage++;
                this.loading = true;
                this.fetchData(this.currentPage);
            }
        },
        prevPage: function() {
            if(this.currentPage > 1){
                this.loading = true;
                this.currentPage--;
                this.fetchData(this.currentPage);
            }
        },
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MMM YYYY')
            }
        },
        get_link(value){
            if(value){
                var url = new URL(value);
                if(this.search){
                    url.searchParams.append('search', this.search);
                }
                url.searchParams.append('sort', this.currentSort);
                url.searchParams.append('sortDir', this.currentSortDir);
                url.searchParams.append('limit', this.currentLimit);
                if(this.filter){
                    for (const key in this.filter) {
                        url.searchParams.append(key, this.filter[key]);
                    }
                }

                return url;
            }
        },
        doSearch : _.debounce(function(){
            this.loading = true;
            this.fetchData();
        }, 1000),
        fetchData(page = 1){
            let params = {
                search : this.search,
                page : page,
                limit : this.currentLimit,
                sort : this.currentSort,
                sortDir : this.currentSortDir
            }

            if(this.filter){
                params = Object.assign(params, this.filter);
            }

            this.$inertia.get(this.route(this.route().current(), params), {
                preserveState: true,
                preserveScroll: true,
                onFinish: () => {
                    this.loading = false;
                },
            });
        },
    }
}
</script>