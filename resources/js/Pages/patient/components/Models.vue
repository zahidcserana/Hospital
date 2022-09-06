<template>
     <div class="patient-list">
        <slot name="header"></slot>
         <div class="card-box">
             <form id="filter-form">

                 <div class="row">
<!--                     <div class="col-md-3">-->
<!--                         <div class="form-group">-->
<!--                             <label> Patient Search </label>-->
<!--                             <input type="text" v-model="query.term"  class="form-control" placeholder="ID, Name, Email...">-->
<!--                         </div>-->
<!--                     </div>-->

                     <div class="col-md-2">
                         <div class="form-group">
                             <label> Search By</label>
                             <select class="form-control select" v-model="query.search_type" >
                                 <option  v-for="row in filter_types" :key="row.key_value" :value="row.key_value">{{ row.key_name }}</option>
                             </select>
                         </div>
                     </div>

                     <div class="col-md-2">
                         <div class="form-group">
                             <label> Search Text</label>
                             <input type="text" v-model="query.search_text"  class="form-control" placeholder="Type search text...">
                         </div>
                     </div>

                     <div class="col-md-2">
                         <div class="form-group">
                             <label> Start Date</label>
                             <datetime  v-model="query.from_date" type="date" input-class="form-control" format="yyyy-MM-dd"></datetime>
                         </div>
                     </div>

                     <div class="col-md-2">
                         <div class="form-group">
                             <label> End Date</label>
                             <datetime   v-model="query.to_date"  type="date" input-class="form-control" format="yyyy-MM-dd"></datetime>
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
                    <table class="table table-border table-striped custom-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Patient Name</th>
                                <th>Patient Code/ID</th>
                                <th>Mobile No.</th>
                                <th>Address</th>
                                <th>Created At</th>
                                <th>Contact Person</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in data.data.data" :key="row.id">
                                <td>{{ row.id }}</td>
                                <td>{{ row.name }}</td>
                                <td>{{ row.code }} / {{ row.id }}</td>
                                <td>{{ row.mobile }}</td>
                                <td>{{ row.address }}</td>
                                <td>{{ row.created_at | formatDate }}</td>
                                <td>{{ row.contact_person_name}} <br/> {{ row.contact_person_mobile}}</td>
                                <td><span class="custom-badge" v-bind:class="row.status == 'ACTIVE' ? 'status-green' : 'status-red'">{{ row.status }}</span></td>

                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                             <jet-responsive-nav-link :href="route('patients.edit', [row.id])" :active="route().current('patients.create')" class="dropdown-item">
                                                <i class="fa fa-pencil m-r-5"></i>Edit
                                            </jet-responsive-nav-link>
                                             <jet-responsive-nav-link :href="route('payments.index', {'patient_id': row.id})" class="dropdown-item">
                                                <i class="fa fa-list m-r-5"></i>Payment History
                                            </jet-responsive-nav-link>
                                             <jet-responsive-nav-link :href="route('payments.create', {'patient_id': row.id})" class="dropdown-item">
                                                <i class="fa fa-money m-r-5"></i>Add Payment
                                            </jet-responsive-nav-link>
                                            <a v-if="$page.isAdmin" class="dropdown-item" href="#" data-toggle="modal" @click="deleteRow(row)"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <br/>
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item"  v-for="row in data.data.links" :key="row.label"  v-bind:class="row.active ? 'active' : ''"><a class="page-link" :href="row.url">{{row.label}}</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import { Datetime } from 'vue-datetime';

 export default {
    components: {
        JetResponsiveNavLink,  datetime: Datetime,
    },
    props: ['data'],
     data() {
         return {
             filter_types: [
                 {'key_value': '', 'key_name': 'All'},
                 {'key_value': 'patient_name', 'key_name': 'Patient Name'},
                 {'key_value': 'patient_code', 'key_name': 'Patient Code'},
                 {'key_value': 'patient_id', 'key_name': 'Patient ID'},
                 {'key_value': 'mobile', 'key_name': 'Mobile No'},
             ],
             query: {
                 term: '',
                 search_type: '',
                 search_text: '',
                 from_date: '',
                 to_date: ''
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
            this.query= {
                 term: '',
                 search_type: '',
                 search_text: '',
                 from_date: '',
                 to_date: ''
             }
        },
        resetData: function () {
            this.resetQuery()
            this.$inertia.get('/patients', this.query)
        },
        getData: function () {
            this.$inertia.get('/patients', this.query)
        },
        deleteRow: function (data) {
            if (!confirm('Are you sure want to remove?')) return;
            data._method = 'DELETE';
            this.$inertia.post('/patients/' + data.id, data)
        }
    }
}
</script>
