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
                                    <select class="form-control select" v-model="query.hospital_id" @change="getDepartment()">
                                        <option value="" selected>-- Hospital --</option>
                                        <option  v-for="(row,index) in param.hospitals" :key="index" :value="row.id">{{ row.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div v-if="departments" class="col-sm-2 col-md-2">
                                <div class="form-group form-focus">
                                    <select class="form-control select" v-model="query.department_id">
                                        <option value="" selected>-- Department --</option>
                                        <option  v-for="(row,index) in departments" :key="index" :value="row.id">{{ row.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 col-md-2">
                                <div class="form-group form-focus">
                                    <input class="form-control" v-model="query.product_for">
                                </div>
                            </div>
                            <div v-if="param.products" class="col-sm-2 col-md-2">
                                <div class="form-group form-focus">
                                    <select class="form-control select mb-2 mr-sm-2"  v-model="query.product_id">
                                    <option value="" selected>Select Product</option>
                                    <option  v-for="row in param.products" :key="row.id" :value="row.id">{{ row.name }}</option>
                                </select>
                                </div>
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
                                <th>Room Number</th>
                                <th>Consumption Qty</th>
                                <th>Consumption For</th>
                                <th>Remarks</th>
                                <th>Created At</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in param.data" :key="row.id">
                                <td>{{ row.id }}</td>
                                <td>{{ row.product.name }}</td>
                                <td>{{ row.room ? row.room.room_number : null }}</td>
                                <td>{{ row.product_qty }}</td>
                                <td>{{ row.product_for }}</td>
                                <td>{{ row.remarks }}</td>
                                <td>{{ row.created_at | formatDateTime }}</td>
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                             <jet-responsive-nav-link :href="route('consumptions.edit', [row.id])" :active="route().current('consumptions.create')" class="dropdown-item">
                                                <i class="fa fa-pencil m-r-5"></i>Edit
                                            </jet-responsive-nav-link>
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

export default {
    components: {
        JetResponsiveNavLink,
    },
    props: ['param', 'errors'],
    data() {
        return {
            departments: null,
            query: ServiceModel.search
        }
    },
    mounted () {
        if (!_.isEmpty(this.param.query)) {
            this.query = this.param.query

            let stop = false
            this.param.hospitals.forEach(row => {
                if(stop){ return; }

                if (row.id == this.query.hospital_id) {
                    this.departments = _.isEmpty(row.departments) ? null : row.departments
                    stop = true;
                }
            })
        }
    },
    methods: {
        resetQuery: function () {
            this.query = {
                hospital_id: '',
                department_id: '',
                product_id: '',
                status: '',
                date_range: undefined,
                product_for: '',
            }
        },
        deleteRow: function (data) {
            if (!confirm('Are you sure want to remove?')) return;
            data._method = 'DELETE';
            this.$inertia.post('/consumptions/' + data.id, data)
        },
        resetData: function () {
            this.resetQuery()
            this.$inertia.get('/consumptions')
        },
        getData: function () {
            this.$inertia.get('/consumptions', this.query)
        },
        getDepartment: function () {
            this.query.department_id = undefined
            let stop = false
            this.param.hospitals.forEach(row => {
                if(stop){ return; }

                if (row.id == this.query.hospital_id) {
                    this.departments = _.isEmpty(row.departments) ? null : row.departments
                    stop = true;
                }
            })
        }
    }
}
</script>
