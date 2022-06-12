<template>
    <BaseLayout title="List Sales">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Sale Payment ({{ dataList.total }})
                <div class="float-right">
                    <button v-if="selected.length > 0 && status != 'done'" type="button" class="btn btn-sm btn-secondary" @click="validateSelected">
                        <i class="si si-check"></i>
                        Validate {{ selected.length }}
                    </button>
                </div>
            </div>
            <div class="block block-rounded block-shadow block-bordered d-md-block d-none mb-10">
                <ul class="nav nav-tabs nav-tabs-alt nav-fill">
                    <li class="nav-item">
                        <a class="nav-link" v-bind:class="{ 'active' : (status == 'pending') ? true : false }" :href="route('admin.sale.payment.index')">Pending
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" v-bind:class="{ 'active' :  (status == 'done') ? true : false }" :href="route('admin.sale.payment.index', { status : 'done' })">Selesai
                        </a>
                    </li>
                </ul>
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
                                <th width="2%" v-if="status != 'done'">
                                    <b-form-checkbox id="selectAll" name="selectAll" v-model="selectAll"></b-form-checkbox>
                                </th>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th v-if="status == 'done'">Validated</th>
                                <th v-else>Payment Due</th>
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
                                    <td v-if="status != 'done'">
                                        <b-form-checkbox
                                            :id="'data-'+data.id"
                                            v-model="selected"
                                            :name="'data-'+data.id"
                                            :value="data.id"
                                            >
                                        </b-form-checkbox>
                                    </td>
                                    <td>{{ format_date(data.date) }}</td> 
                                    <td>{{ data.ref }}</td>
                                    <td>{{ data.customer_name }}</td>
                                    <td>{{ currency(data.amount) }}</td>
                                    <td v-if="status == 'done'">
                                        {{ data.validated_at }}<br>
                                        {{ data.staff_name }}
                                    </td>
                                    <td v-else>{{ format_date(data.payment_due) }}</td>
                                    <th>
                                        <button type="button" @click="show(data)" class="btn btn-secondary btn-sm">
                                            <i class="si si-eye mr-1"></i>
                                            Detail
                                        </button>
                                    </th>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-if="!Object.values(dataList.data).length">
                                    <td :colspan="status == 'done' ? 6 : 7">
                                        <div class="text-center">
                                            <img class="img-fluid" :src="asset('images/not_found.png')">
                                            <h3 class="font-size-24 font-w600 mt-3">Data Tidak Ditemukan</h3>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <b-modal id="detail" ref="detail" size="md" content-class="rounded" body-class="p-0" no-close-on-backdrop hide-footer hide-header>
            <div class="block block-rounded block-transparent mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Detail Payment</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" @click="$bvModal.hide('detail')">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content py-15" v-if="data">
                    <div class="d-flex">
                        <div class="mr-3 mb-2">Tanggal Pembayaran</div>
                        <div class="font-w700">: {{ data.date }}</div>
                    </div>
                    <div class="d-flex">
                        <div class="mr-3 mb-2">No Invoice</div>
                        <div class="font-w700">: <a :href="route('admin.sale.order.show', { 'id' : data.sale_id})">{{ data.ref }}</a></div>
                    </div>
                    <div class="d-flex">
                        <div class="mr-3 mb-2">Customer</div>
                        <div class="font-w700">: {{ data.customer_name }}</div>
                    </div>
                    <div class="d-flex">
                        <div class="mr-3 mb-2">Jumlah Pembayaran</div>
                        <div class="font-w700">: {{ currency(data.amount) }}</div>
                    </div>
                    <div class="d-flex">
                        <div class="mr-3 mb-2">Metode Pembayaran</div>
                        <div class="font-w700">: {{ data.payment_method.type }} ({{ data.payment_method.name }})</div>
                    </div>
                    <template v-if="data.payment_proof">
                        <div class="d-flex">
                            <div class="mr-3 mb-2">Bukti Pembayaran</div>
                            <div class="font-w700">:</div>
                        </div>
                        <img :src="data.payment_proof.url" class="img-fluid">
                    </template>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-noborder btn-block" @click.prevent="validate(data)">
                    <i class="fa fa-check"></i> Validate
                </button>
            </div>
        </b-modal>
    </BaseLayout>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import moment from 'moment';
import _ from 'lodash';
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
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
            status : this.route().params.status == undefined ? 'pending' : this.route().params.status,
            search : this.route().params.search == undefined ? '' : this.route().params.search,
            currentPage: this.route().params.page == undefined ? 1 : this.route().params.page,
            data : null,
        } 
    },
    props: {
        dataList: Object,
    },
    watch: {
        selectAll: function(val){
            this.selected = [];
            if(val){
                this.dataList.data.forEach((value, index) => {
                    this.selected.push(value.id)
                });
            }
        }
    },
    methods :{
        checkPaginate(type){
            const vm = this;
            if(vm.dataList){
                if(type == 'next'){
                    return (vm.dataList.next_page_url) ? false : true;
                }else{
                    return (vm.dataList.prev_page_url) ? false : true;
                }
            }else{
                return true;
            }
        },
        nextPage: function() {
            if(this.currentPage < this.dataList.total){
                this.currentPage++;

                let params = {
                    status : this.status,
                    search : this.search,
                    page : this.currentPage,
                }
                
                this.$inertia.get(this.route('admin.sale.payment.index', params), {
                    preserveScroll : true,
                });
            }
        },
        prevPage: function() {
            if(this.currentPage > 1){
                this.currentPage--;

                let params = {
                    status : this.status,
                    search : this.search,
                    page : this.currentPage,
                }
                
                this.$inertia.get(this.route('admin.sale.payment.index', params), {
                    preserveScroll : true,
                });
            }
        },
        format_date(value) {
            if (value) {
                moment.locale('id');
                return moment(String(value)).format('DD MMM YYYY h:mm')
            }
        },
        doSearch: _.throttle(function () {
            this.$inertia.get(this.route('admin.sale.payment.index', {
                status : this.status,
                search: this.search,
                page : 1,
            }), {
                preserveScroll: true,
            })
        }, 1000),
        select() {
            this.selected = [];
            if (!this.selectAll) {
                this.dataList.data.forEach((value, index) => {
                    this.selected.push(value.id)

                });
            }
        },
        validateSelected(){
            this.$swal.fire({
                icon: 'warning',
                title: 'Anda Yakin?',
                text: `${ this.selected.length } data akan divalidasi!`,
                showCancelButton: true,
                cancelButtonText : 'Tidak, Batal',
                confirmButtonText: "Ya, Validasi",
                confirmButtonColor: '#3f9ce8',
                cancelButtonColor: '#af1310',
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.post(this.route('admin.sale.payment.validateAll'), {
                            ids : this.selected,
                        }, {
                        preserveScroll: true,
                        resetOnSuccess: false,
                        onProgress: ()=> {
                            this.$swal.fire({
                                title: 'Tunggu Sebentar...',
                                text: '',
                                imageUrl: window._asset + 'media/loading.gif',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                            });
                        },
                        onSuccess: () => {
                            this.$swal.Close();
                            return this.$swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: "Pembayaran Berhasil Divalidasi",
                                showCancelButton: false,
                                showConfirmButton: false,
                            });
                        },
                        onError:() => {
                            this.$swal.Close();
                        }
                    })
                }
            })
        },
        validate(value){
            this.$inertia.post(this.route('admin.sale.payment.validate'), {
                    payment_id : value.id,
                    sale_id : value.sale_id
                }, {
                preserveScroll: true,
                resetOnSuccess: false,
                onProgress: ()=> {
                    this.$swal.fire({
                        title: 'Tunggu Sebentar...',
                        text: '',
                        imageUrl: window._asset + 'media/loading.gif',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    });
                },
                onSuccess: () => {
                    this.$swal.close();
                    this.data = null;
                    this.$bvModal.hide("detail");
                    return this.$swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: "Pembayaran Berhasil Divalidasi",
                        showCancelButton: false,
                        showConfirmButton: false,
                    });
                },
                onError:() => {
                    this.$swal.close();
                }
            })
        },
        show(value){
            this.data = value;
            this.$bvModal.show("detail");
        }
    }
}
</script>
