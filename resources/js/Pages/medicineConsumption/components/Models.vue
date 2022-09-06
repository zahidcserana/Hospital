<template>
     <div class="hospital-list">
         <slot name="header"></slot>

         <div v-if="param.hospitals" class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <form id="filter-form" ref="filter-form">
                        <div class="row filter-row">
                            <div class="col-sm-2 col-md-2">
                                <div class="form-group form-focus">
                                    <select class="form-control select" v-model="query.hospital_id">
                                        <option value="" selected>-- Hospital --</option>
                                        <option  v-for="(row,index) in param.hospitals" :key="index" :value="row.id">{{ row.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 col-md-2">
                                <multiselect v-model="query.medicine_id" id="ajax" label="name" track-by="name" :placeholder="medicinePlaceholder" open-direction="bottom" :options="products" :custom-label="productLabel" :searchable="true" :loading="isLoading" @search-change="searchMedicine"></multiselect>
                            </div>
                            <div class="col-sm-2 col-md-2">
                                <div class="form-group form-focus">
                                    <date-picker v-model="query.date_range" valueType="format" range></date-picker>
                                </div>
                            </div>
                            <div class="col-sm-1 col-md-1">
                                <button type="button" @click="getData()" class="btn btn-success btn-block"> Search </button>
                            </div>
                            <div class="col-sm-1 col-md-1">
                                <button type="button" @click="resetData()" class="btn btn-warning btn-block"> Reset </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-border table-striped custom-table datatable mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Consumption Qty</th>
                                <th>Consumption For</th>
                                <th>Remarks</th>
                                <th>Date</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in param.data" :key="row.id">
                                <td>{{ row.id }}</td>
                                <td>{{ row.medicine.name }} ({{ row.medicine.strength }})</td>
                                <td>{{ row.quantity }}</td>
                                <td>{{ row.product_for }}</td>
                                <td>{{ row.remarks }}</td>
                                <td>{{ row.date | myDateFormat }}</td>
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a v-if="$page.isAdmin" class="dropdown-item" href="#" data-toggle="modal" @click="deleteRow(row)"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import ServiceModel from './ServiceModel'
import {HTTP} from '@/utils/http-common'

export default {
    components: {
        JetResponsiveNavLink,
    },
    props: ['param', 'errors'],
    data() {
        return {
            products: [],
            search: { term: ''},
            isLoading: false,
            departments: null,
            query: ServiceModel.search,
            medicinePlaceholder: 'Type to search'
        }
    },
    mounted () {
        if (!_.isEmpty(this.param.query)) {
            this.query = this.param.query

            if (!_.isEmpty(this.param.query.medicine)) {
                this.query.medicine_id = this.param.query.medicine
            }
        }

        if (this.param.query.length === 0) {
            this.resetQuery()
        }
    },
    methods: {
        resetQuery: function () {
            this.query = {
                hospital_id: '',
                medicine_id: '',
                date_range: undefined,
            }
        },
        deleteRow: function (data) {
            if (!confirm('Are you sure want to remove?')) return;
            data._method = 'DELETE';
            this.$inertia.post('/medicine-consumption/' + data.id, data)
        },
        resetData: function () {
            this.resetQuery()
            this.$inertia.get('/medicine-consumption')
        },
        getData: function () {
            if (this.query.medicine_id !== undefined) {
                this.query.medicine_id = this.query.medicine_id.id
            }

            this.$inertia.get('/medicine-consumption', this.query)
        },
         productLabel ({ name, strength }) {
            return `${name} — (${strength})`
        },
        searchMedicine (query) {
            this.isLoading = true
            this.search.term = query
            HTTP.post('/searchMedicine', this.search).then(response => {
                this.products = response.data
                this.isLoading = false
            })
        }
    }
}
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
