<template>
    <b-modal id="pos-recent-transaction" ref="pos-recent-transaction" size="xl" content-class="rounded" body-class="p-0" no-close-on-backdrop hide-footer hide-header>
        <div class="block block-rounded block-transparent mb-0">
            <div class="block-header block-header-default">
                <h3 class="block-title">Recent Transaction</h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option" @click="close()">
                        <i class="fa fa-fw fa-times"></i>
                    </button>
                </div>
            </div>
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
                                <span v-if="dataList">{{ dataList.from }}-{{ dataList.to }}/{{ dataList.total }}</span>
                            </div>
                            <div class="pt-25 pl-0">
                                <button type="button" class="btn btn-alt-secondary mx-1" @click.prevent="prevPage"
                                    :disabled="(dataList && dataList.prev_page_url) ? false : true">
                                <i class="fa fa-chevron-left fa-fw"></i>
                                </button>
                                <button class="btn btn-alt-secondary mx-1" type="button" @click.prevent="nextPage"
                                    :disabled="(dataList &&  dataList.next_page_url) ? false : true">
                                <i class="fa fa-chevron-right fa-fw"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block-content p-0">
                <table class="table table-striped table-vcenter table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Invoice No</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Total Items</th>
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
                                <td>{{ data.date }}</td> 
                                <td>{{ data.ref }}</td>
                                <td>{{ data.customer.name }}</td>
                                <td>{{ currency(data.grand_total) }}</td>
                                <td>{{ data.line_count }}</td>
                                <th>
                                    <a :href="route('admin.sale.order.show', {id : data.id})" class="btn btn-secondary btn-sm" target="_blank">
                                        <i class="si si-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-secondary btn-sm" @click="$parent.openInvoice(data.id)">
                                        <i class="fi fi-rs-print"></i>
                                    </button>
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
    </b-modal>
</template>
<script>
import axios from 'axios';
import moment from 'moment';
export default {
    name : 'pos-recent-transaction',
    data(){
        return {
            dataList : null,
            search : "",
            page : 1,
            loading : true,
        }
    },
    props :{
        open : Boolean,
    },
    methods : {
        close(){
            this.dataList = null,
            this.$bvModal.hide('pos-recent-transaction');
        },
        openModal(){
            this.fethData();
            this.$bvModal.show('pos-recent-transaction');
        },
        nextPage: function() {
            if(this.page < this.dataList.total){
                this.page++;
                this.fethData();
            }
        },
        prevPage: function() {
            if(this.page > 1){
                this.page--;
                this.fethData();
            }
        },
        doSearch: _.throttle(function () {
            this.fethData();
        }, 200),
        async fethData(){
            const self = this;
            await axios.get(this.route("admin.sale.pos.transaction"),{
                params: {
                    page: this.page,
                    search : this.search
                }
            })
            .then(function (response) {
                self.loading = false;
                self.dataList = response.data;
            })
            .catch(function (error) {
                
            })
        },
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MM YYYY hh:mm')
            }
        },
    }
}
</script>