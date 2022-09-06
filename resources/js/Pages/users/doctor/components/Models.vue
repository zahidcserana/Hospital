<template>
     <div class="hospital-list">
         <slot name="header"></slot>
         <div class="card-box">
             <form id="filter-form">

                 <div class="row">

                     <div class="col-md-3">
                         <div class="form-group">
                             <label> Search By</label>
                             <select class="form-control select" v-model="query.search_type" >
                                 <option  v-for="row in filter_types" :key="row.key_value" :value="row.key_value">{{ row.key_name }}</option>
                             </select>
                         </div>
                     </div>

                     <div class="col-md-3">
                         <div class="form-group">
                             <label> Search Text</label>
                             <input type="text"   v-model="query.search_text"  class="form-control" placeholder="Type search text...">
                         </div>
                     </div>

                     <div class="col-md-3">
                         <div class="form-group">
                             <label> Speciality</label>
                             <select class="form-control select" v-model="query.speciality_id" >
                                 <option value="">All</option>
                                 <option  v-for="row in data.specialities" :key="row.id" :value="row.id">{{ row.name }}</option>
                             </select>
                         </div>
                     </div>

                     <div class="col-sm-2 col-md-1">
                         <label for="">&nbsp;</label>
                         <button type="button" @click="getData()" class="btn btn-success btn-small"> Search </button>
                     </div>
                     <div class="col-sm-2 col-md-1">
                         <label for="">&nbsp;</label>
                         <button type="button" @click="resetData()" class="btn btn-warning btn-small"> Reset </button>
                     </div>
                 </div>
             </form>
         </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-border table-striped custom-table datatable mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Code/ID</th>
                                <th>Designation</th>
                                <th>Speciality</th>
                                <th>Type</th>
                                <th>Hospital Name</th>
                                <th>BMDC Reg No.</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in data.data" :key="row.id">
                                <td>{{ row.id }}</td>
                                <td>{{ row.name }} </td>
                                <td>({{ row.code }})/{{ row.id }}</td>
                                <td>{{ row.designation ? row.designation.title : null }}</td>
                                <td>{{ row.speciality ? row.speciality.name : null }}</td>
                                <td>{{ row.type }}</td>
                                <td>{{ row.hospital ? row.hospital.name : null}}</td>
                                <td>{{ row.bmdc_id}}</td>
                                <td><span class="custom-badge" v-bind:class="row.status == 'ACTIVE' ? 'status-green' : 'status-red'">{{ row.status }}</span></td>

                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                             <jet-responsive-nav-link :href="route('doctors.edit', [row.id])" :active="route().current('doctors.create')" class="dropdown-item">
                                                <i class="fa fa-pencil m-r-5"></i>Edit
                                            </jet-responsive-nav-link>
<!--                                            <a v-if="$page.isAdmin" class="dropdown-item" href="#" data-toggle="modal" @click="deleteRow(row)"><i class="fa fa-trash-o m-r-5"></i> Delete</a>-->
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

 export default {
     components: {
            JetResponsiveNavLink,
        },
        props: ['data', 'errors'],
        data() {
            return {
                filter_types: [
                    {'key_value': '', 'key_name': 'All'},
                    {'key_value': 'doctor_name', 'key_name': 'Doctor Name'},
                    {'key_value': 'doctor_code', 'key_name': 'Doctor Code'},
                    {'key_value': 'doctor_id', 'key_name': 'Doctor ID'},
                    {'key_value': 'mobile', 'key_name': 'Mobile No'},
                ],
                query: {
                    search_type: '',
                    search_text: '',
                    speciality_id: ''
                }
            }
        },
     mounted() {
         if (!_.isEmpty(this.data.query)) {
             this.query = this.data.query
         }
     },
        methods: {
            resetQuery: function () {
                this.query = null
            },
            resetData: function () {
                this.resetQuery()
                this.$inertia.get('/doctors', this.query)
            },
            getData: function () {
                this.$inertia.get('/doctors', this.query)
            },
            deleteRow: function (data) {
                if (!confirm('Are you sure want to remove?')) return;
                data._method = 'DELETE';
                this.$inertia.post('/doctors/' + data.id, data)
            }
        }
    }
</script>
