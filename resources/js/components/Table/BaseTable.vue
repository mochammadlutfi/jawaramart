<template>
    <div>
        <div class="block block-rounded block-shadow block-bordered d-md-block d-none mb-10">
            <div class="block-content p-2">
                <div class="row justify-content-between">
                    <div class="col-4">
                        <div class="has-search">
                            <i class="fa fa-search"></i>
                            <input type="search" class="form-control" id="search-data-list" v-model="search" @input="doSearch" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-4">
                    </div>
                    <div class="col-4">
                        <div class="d-flex float-right">
                            <div class="my-auto px-3">
                                <span>{{ values.from }}-{{ values.to }}/{{ values.total }}</span>
                            </div>
                            <div class="pt-25 pl-0">
                                <button @click="prevPage" class="btn btn-alt-secondary mx-1" type="button"
                                :disabled="checkPaginate('prev')">
                                    <i class="fa fa-chevron-left fa-fw"></i>
                                </button>
                                <button @click="nextPage" class="btn btn-alt-secondary mx-1" type="button"
                                :disabled="checkPaginate('next')">
                                    <i class="fa fa-chevron-right fa-fw"></i>
                                </button>
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
                            <th width="2%" v-if="checkbox">
                                <slot name="rowCheck"></slot>
                            </th>
                            <table-head v-for="(th, thi) in columns" :key="thi" :name="th.field" :width="th.width">
                                {{ th.name }}
                            </table-head>
                            <th width="8%"></th>
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
                            <slot name="body" :values="values.data" :selectRow="selectRow"></slot>
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
        </div>
    </div>
</template>
<script>
import TableHead from "./TableHead.vue";

export default {
    name : 'base-table',
    components : {
        TableHead
    },
    props : {
        columns : Array,
        values : Object,
        checkbox : Boolean,
    },
    data(){
        return {
            loading : false,
            selected: [],
            search : this.route().params.search == undefined ? '' : this.route().params.search,
            currentSort: this.route().params.sort == undefined ? 'name' : this.route().params.sort,
            currentSortDir: this.route().params.sortDir == undefined ? 'asc' : this.route().params.sortDir,
            currentPage: this.route().params.page == undefined ? 1 : this.route().params.page,
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
    methods :{
        sortBy:function(s) {

            if(s === this.currentSort) {
                this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' :'asc';
            }

            this.loading = true;
            this.currentSort = s;
            this.fetchData();
        },
        checkPaginate(type){
            const vm = this;
            if(vm.values){
                if(type == 'next'){
                    return (vm.values.next_page_url) ? false : true;
                }else{
                    return (vm.values.prev_page_url) ? false : true;
                }
            }else{
                return true;
            }
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
        doSearch : _.debounce(function(){
            this.loading = true;
            this.fetchData();
        }, 1000),
        fetchData(page = 1){
            let params = {
                search : this.search,
                page : page,
                sort : this.currentSort,
                sortDir : this.currentSortDir
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