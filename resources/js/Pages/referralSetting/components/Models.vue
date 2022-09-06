<template>
     <div class="patient-list">
        <slot name="header"></slot>


        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-border table-striped custom-table datatable mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Person Name</th>
                                <th>Service Type</th>
                                <th>Item Type</th>
                                <th>Service Name</th>
                                <th>Percentage Value</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in param.data" :key="row.id">
                                <td>{{ row.id }}</td>
                                <td>{{ row.person.name }} </td>
                                <td>{{ row.service_type }}</td>
                                <td>{{ row.item_type }}</td>
                                <td>{{ row.item ? row.item.name : null }}</td>
                                <td>{{ row.percentage_value }}</td>
                                <td><span class="custom-badge" v-bind:class="row.status == 'ACTIVE' ? 'status-green' : 'status-red'">{{ row.status }}</span></td>
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                             <jet-responsive-nav-link :href="route('referralSetting.edit', [row.id])" class="dropdown-item">
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
import {HTTP} from "@/utils/http-common";

 export default {
    components: {
        JetResponsiveNavLink
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
             ],
             query: {
                 search_type: '',
                 search_text: ''
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
        resetQuery: function () {
            this.query = null
        },
        deleteRow: function (data) {
            if (!confirm('Are you sure want to remove?')) return;
            data._method = 'DELETE';
            this.$inertia.post('/referralSetting/' + data.id, data)
        }
    }
}
</script>
