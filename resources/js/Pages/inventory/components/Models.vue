<template>
     <div class="hospital-list">
        <slot name="header"></slot>

        <div v-if="param.hospitals" class="row">
            <div class="col-md-12">
                <div class="card-box">
                    <form id="filter-form">
                        <input type="hidden" name="type" :value="param.type">
                        <div class="row filter-row">
                            <div v-if="$page.isAdmin" class="col-sm-6 col-md-3">
                                <div class="form-group form-focus">
                                    <select class="form-control select"  v-model="query.hospital_id" @change="param.type==2?getDepartment(): false">
                                        <option value="0" selected>-- Hospital --</option>
                                        <option  v-for="(row,index) in param.hospitals" :key="index" :value="row.id">{{ row.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div v-if="departments" class="col-sm-6 col-md-3">
                                <div class="form-group form-focus">
                                    <select class="form-control select"  v-model="query.department_id">
                                        <option value="0" selected>-- Department --</option>
                                        <option  v-for="(row,index) in departments" :key="index" :value="row.id">{{ row.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div v-if="param.products" class="col-sm-2 col-md-2">
                                <div class="form-group form-focus">
                                    <select class="form-control select mb-2 mr-sm-2"  v-model="query.product_id">
                                    <option value='' selected>Select Product</option>
                                    <option  v-for="row in param.products" :key="row.id" :value="row.id">{{ row.name }}</option>
                                </select>
                                </div>
                            </div>
                            <div class="col-sm-3 col-md-2">
                                <button
                                    type="button"
                                    @click="getData()"
                                    class="btn btn-success btn-block"
                                >
                                    Search
                                </button>
                            </div>
                            <div class="col-sm-3 col-md-2">
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
                        <caption v-if="param.type==1">Hospital: {{ param.hospital.name }}</caption>
                        <caption v-if="param.type==2">Department: {{ param.department.name }}, {{ param.department.hospital.name }}</caption>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th> Generic</th>
                                <th> Description</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in param.data" :key="row.id">
                                <td>{{ row.id }}</td>
                                <td>{{ row.product.name }}</td>
                                <td>{{ row.product.product_category.name }}</td>
                                <td>{{ row.product.generic_name }}</td>
                                <td>{{ row.product.description }}</td>
                                <td>{{ row.quantity }}</td>
                                <td><span class="custom-badge" v-bind:class="row.status == 'ACTIVE' ? 'status-green' : 'status-red'">{{ row.status }}</span></td>

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

 export default {
    components: {
        JetResponsiveNavLink
    },
    props: ['param'],
    data () {
        return {
            departments: null,
            query: ServiceModel.search
        }
    },
    mounted () {
        if (!_.isEmpty(this.param.query)) {
            this.query = this.param.query

            if (this.param.type == 2) {
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

        if (this.param.query.length === 0) {
            this.resetQuery()
        }
    },
    methods: {
        resetQuery: function () {
            this.query = {
                hospital_id: '',
                department_id: '',
                status: '',
                product_id: '',
            }
        },
        deleteRow: function (data) {
            if (!confirm('Are you sure want to remove?')) return;
            data._method = 'DELETE';
            this.$inertia.post('/inventories/' + data.id, data)
        },
        getData: function () {
            if (this.param.type == 2) {
                if (this.query.department_id == undefined) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please select a department',
                        showConfirmButton: false,
                        timer: 1500
                    })
                } else {
                    this.$inertia.get('/dept-inventories', this.query)
                }
            } else {
                this.$inertia.get('/inventories', this.query)
            }
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
        },
        resetData: function () {
            this.resetQuery()
            if (this.param.type == 2) {
                this.$inertia.get('/dept-inventories')
            } else {
                this.$inertia.get('/inventories')
            }
        }
    }
}
</script>

<style>
caption {
  display: table-caption;
  text-align: center;
  caption-side: top;
}
</style>