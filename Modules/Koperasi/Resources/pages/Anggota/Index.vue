<template>
    <BaseLayout title="Anggota List">
        <div class="content">
            <div class="content-heading pt-0 mb-3">
                Anggota List
                <div class="float-right">
                    <button v-if="selected.length > 0" type="button" class="btn btn-sm btn-danger" >
                        <i class="si si-trash"></i>
                        Delete {{ selected.length }} Selected
                    </button>
                    <a class="btn btn-sm btn-secondary" :href="route('admin.kop.anggota.create')">
                        <i class="si si-plus"></i>
                        Tambah
                    </a>
                </div>
            </div>

            <base-table :values="dataList" :columns="columns" checkbox>
                <template #rowCheck>
                    <b-form-checkbox id="selectAll" name="selectAll" @change="selectAll"></b-form-checkbox>
                </template>
                <template #body="data">
                    <tr v-for="value in data.values" :key="value.id">
                        <td>
                            <b-form-checkbox
                                :id="'data-'+value.anggota_id"
                                v-model="selected"
                                :name="'data-'+value.anggota_id"
                                :value="value.anggota_id"
                                >
                            </b-form-checkbox>
                        </td>
                        <td>{{ value.anggota_id }}</td>
                        <td>{{ value.nama }}</td>
                        <td>
                            <div class="font-size-16 font-w600">No HP : {{ value.no_hp == null ? "—" :  value.no_hp }}</div>
                            <div class="font-size-15">Email : {{ value.email == null ? "—" :  value.email }}</div>
                        </td>
                        <td>
                            <!-- <template v-if="data.alamat.length">
                                {{ data.alamat[0].alamat_lengkap }}
                            </template>
                            <template v-else> -->
                                —
                            <!-- </template> -->
                        </td>
                        <td>{{ format_date(value.tgl_gabung) }}</td>
                        <td>
                            <!-- <a :href="route('admin.customer.show', { id : value.id})" class="btn btn-sm btn-secondary">
                                <i class="si si-eye mr-1"></i>Detail
                            </a> -->
                        </td>
                    </tr>
                </template>
            </base-table>

        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from '@/Layouts/Backend/Authenticated.vue';
import moment from 'moment';
import BaseTable from '@/components/Table/BaseTable.vue';

export default {
    components: {
        BaseLayout,
        BaseTable
    },
    data(){
        return {
            columns : [
                {
                    name : "ID Anggota",
                    field : "anggota_id",
                    width : "11%",
                },
                {
                    name : 'Nama',
                    field : 'name',
                    width : "19%",
                },
                {
                    name : "Kontak",
                    field : 'kontak',
                    width : "15%",
                },
                {
                    name : "Alamat",
                    field : 'alamat',
                    width : "25%",
                },
                {
                    name : "Tgl Gabung",
                    field : 'registered_at',
                    width : "15%",
                }
            ],
            selected : [],
        } 
    },
    watch: {
        search: function () {
            this.doSearch() 
        },
    },
    props: {
        dataList: Object,
    },
    methods :{
        selectAll(e){
            if(e){
                this.dataList.data.forEach((v, i) => {
                    this.selected.push(v.anggota_id)
                });
            }else{ 
                this.selected = [];
            }
        },
        format_date(value){
            if (value) {
                return moment(String(value)).format('DD MMM YYYY')
            }
        },
    }
}
</script>
