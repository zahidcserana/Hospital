<template>
     <div class="patient-list">
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
                                <th>Bed No.</th>
                                <th>Admission Date</th>
                                <th>Release Date</th>
                                <th>Staying Days</th>
                                <th>Contact Person</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in param.data.data" :key="row.id">
                                <td>{{ row.id }}</td>
                                <td>{{ row.patient.name }} </td>
                                <td>{{ row.bill_no }} / {{ row.patient.id }}</td>
                                <td>{{ row.room }}</td>
                                <td>{{ row.admission_date | formatDateTime }}</td>
                                <td>{{ row.release_date | formatDateTime }}</td>
                                <td>{{ getStayingDays(row.admission_date, row.release_date) }}</td>
                                <td>{{ row.patient.contact_person_name }} <br/> {{ row.patient.contact_person_mobile }}</td>
                                <td><span class="custom-badge" v-bind:class="row.status == 'Admission' ? 'status-green' : 'status-red'">{{ row.status }}</span></td>

                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                             <jet-responsive-nav-link :href="route('patientAdmissions.edit', [row.id])" :active="route().current('patientAdmissions.create')" class="dropdown-item">
                                                <i class="fa fa-pencil m-r-5"></i>Edit
                                            </jet-responsive-nav-link>

                                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#receipt"  @click="getFacesheet(row)"><i class="fa fa-print m-r-5"></i> Face Sheet</a>

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
                            <li class="page-item"  v-for="row in param.data.links" :key="row.label"  v-bind:class="row.active ? 'active' : ''"><a class="page-link" :href="row.url">{{row.label}}</a></li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!--        invoice modal section start-->

            <!-- Modal -->
            <div class="modal fade  bd-example-modal-lg" id="receipt" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

                <facesheet :admission="admission" />
            </div>

            <!--         invoice modal section end-->

        </div>
    </div>
</template>

<script>
import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
import {HTTP} from "@/utils/http-common";
import Facesheet from './Facesheet'
import moment from 'moment'
import { Datetime } from 'vue-datetime';
import Invoice from "@/Pages/billing/payment/components/Invoice";

 export default {
    components: {
        JetResponsiveNavLink, Facesheet, datetime: Datetime
    },
     data() {
         return {
             admission: {},
             filter_types: [
                 {'key_value': '', 'key_name': 'All'},
                 {'key_value': 'patient_name', 'key_name': 'Patient Name'},
                 {'key_value': 'patient_code', 'key_name': 'Patient Code'},
                 {'key_value': 'patient_id', 'key_name': 'Patient ID'},
                 {'key_value': 'mobile', 'key_name': 'Mobile No'},
                 {'key_value': 'staying_days', 'key_name': 'Staying Days'},
             ],
             query: {
                 search_type: '',
                 search_text: '',
                 from_date: undefined,
                 to_date: undefined
             }
         }
     },
    props: ['param'],
     mounted() {
         if (!_.isEmpty(this.param.query)) {
             this.query = this.param.query
         }
     },
    methods: {
        getStayingDays(admission_date, release_date){
            var startDate = moment(admission_date, 'YYYY-MM-DD HH:mm:ss');
            var endDate = moment(release_date, 'YYYY-MM-DD HH:mm:ss');
            if(release_date){
                return endDate.diff(startDate, 'days');
            }
            return "N/A"

        },
        resetQuery: function () {
            this.query = null
        },
        resetData: function () {
            this.resetQuery()
            this.$inertia.get('/patientAdmissions', this.query)
        },
        getData: function () {
            this.$inertia.get('/patientAdmissions', this.query)
        },
        deleteRow: function (data) {
            if (!confirm('Are you sure want to remove?')) return;
            data._method = 'DELETE';
            this.$inertia.post('/patientAdmissions/' + data.id, data)
        },
        getFacesheet: function (admission) {
            HTTP.get('/patientAdmissions/'+admission.id+'/facesheet')
                .then((response)=>{
                    this.admission = response.data;
                })
        }
    }
}
</script>
