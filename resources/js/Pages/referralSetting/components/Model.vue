<template>
    <div class="patient-create">
        <slot name="header"></slot>

        <div class="row  card-box">
            <div class="col-lg-8 offset-lg-2">
                <div class="card-box">
                    <form>
                        <div class="row">

                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label>Person <span class="text-red">*</span></label>
                                    <Select2
                                        v-model="form.person_id"
                                        placeholder="Select Person"
                                        :options="param.doctors"  />
                                    <div class="text-danger" v-if="errors.person_id">{{ errors.person_id[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-8">
                                <div class="form-group">
                                    <label>Service Type <span class="text-red">*</span></label>
                                    <select class="form-control select"  v-model="form.service_type" :disabled="editMode">
                                        <option  v-for="row in service_types" :key="row.value" :value="row.value">{{ row.name }}</option>
                                    </select>
                                    <div class="text-danger" v-if="errors.service_type">{{ errors.service_type[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-8"  v-if="form.service_type=='OPD' || form.service_type=='IPD' ">
                                <div class="form-group">
                                    <label>Item Type </label>
                                    <select class="form-control select"  v-model="form.item_type" :disabled="editMode">
                                        <option  v-for="row in item_types" :key="row" :value="row">{{ row }}</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-8"  v-if="form.service_type=='OPD' && editMode">
                                <div class="form-group" v-if="form.item_type=='SERVICE'">
                                    <label>Service <span class="text-red">*</span></label>
                                    <Select2
                                        v-model="form.service_id"
                                        placeholder="Select Service"
                                        :options="param.opd_services"  />
                                    <div class="text-danger" v-if="errors.service_id">{{ errors.service_id[0] }}</div>
                                </div>
                                <div class="form-group" v-if="form.item_type!='SERVICE'">
                                    <label>Package <span class="text-red">*</span></label>
                                    <Select2
                                        v-model="form.service_id"
                                        placeholder="Select Service"
                                        :options="param.opd_packages"  />
                                    <div class="text-danger" v-if="errors.service_id">{{ errors.service_id[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-8"  v-if="form.service_type=='IPD' && editMode">
                                <div class="form-group"  v-if="form.item_type=='SERVICE'">
                                    <label>Service <span class="text-red">*</span></label>
                                    <Select2
                                        v-model="form.service_id"
                                        placeholder="Select Service"
                                        :options="param.ipd_services"  />
                                    <div class="text-danger" v-if="errors.service_id">{{ errors.service_id[0] }}</div>
                                </div>
                                <div class="form-group"  v-if="form.item_type!='SERVICE'">
                                    <label>Package <span class="text-red">*</span></label>
                                    <Select2
                                        v-model="form.service_id"
                                        placeholder="Select Service"
                                        :options="param.ipd_packages"  />
                                    <div class="text-danger" v-if="errors.service_id">{{ errors.service_id[0] }}</div>
                                </div>
                            </div>

                            <div class="col-sm-8" v-if="form.service_type=='OPDALL' || form.service_type=='IPDALL' || editMode">
                                <div class="form-group">
                                    <label>Percentage Value</label>
                                    <input class="form-control" type="number" v-model="form.percentage_value" min="0"/>
                                </div>
                            </div>

                            <div class="col-sm-12" v-if="form.service_type!='OPDALL' && form.service_type!='IPDALL' && form.service_type == 'OPD'  && form.item_type == 'SERVICE' && !editMode">
                                <table>
                                    <tr><th>Service Name</th> <th>Percentage Value</th></tr>
                                    <tr v-for="row in param.opd_services">
                                        <td>{{row.text}}</td>
                                        <td><input class="form-control" type="number" v-model="row.percentage_value" min="0"/></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-sm-12" v-if="form.service_type!='OPDALL' && form.service_type!='IPDALL' && form.service_type == 'OPD'  && form.item_type == 'PACKAGE' && !editMode">
                                <table>
                                    <tr><th>Package Name</th> <th>Percentage Value</th></tr>
                                    <tr v-for="row in param.opd_packages">
                                        <td>{{row.text}}</td>
                                        <td><input class="form-control" type="number" v-model="row.percentage_value" min="0"/></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-sm-12" v-if="form.service_type!='OPDALL' && form.service_type!='IPDALL' && form.service_type == 'IPD' && form.item_type == 'SERVICE'  && !editMode">
                                <table>
                                    <tr><th>Service Name</th> <th>Percentage Value</th></tr>
                                    <tr v-for="row in param.ipd_services">
                                        <td>{{row.text}}</td>
                                        <td><input class="form-control" type="number" v-model="row.percentage_value" min="0"/></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-sm-12" v-if="form.service_type!='OPDALL' && form.service_type!='IPDALL' && form.service_type == 'IPD' && form.item_type == 'PACKAGE'  && !editMode">
                                <table>
                                    <tr><th>Package Name</th> <th>Percentage Value</th></tr>
                                    <tr v-for="row in param.ipd_packages">
                                        <td>{{row.text}}</td>
                                        <td><input class="form-control" type="number" v-model="row.percentage_value" min="0"/></td>
                                    </tr>
                                </table>
                            </div>


                            <div class="col-sm-8"  >
                                <div class="form-group">
                                    <label>Status <span class="text-red">*</span></label>
                                    <select class="form-control select" v-model="form.status">
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="INACTIVE">INACTIVE</option>
                                    </select>
                                    <div class="text-danger" v-if="errors.status">{{ errors.status[0] }}</div>

                                </div>
                            </div>

                        </div>
                        <div class="m-t-20 text-center">
                            <button
                                type="button"
                                class="btn btn-primary submit-btn"
                                wire:click.prevent="store()"
                                v-show="!editMode"
                                @click="save(form)"
                            >
                                Save
                            </button>
                            <button
                                type="button"
                                class="btn btn-primary submit-btn"
                                wire:click.prevent="store()"
                                v-show="editMode"
                                @click="update(form)"
                            >
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Alert from '@/Pages/Component/Alert'
import moment from "moment";
import Select2 from 'v-select2-component';

export default {
    components: {
        Alert,
        Select2
    },
    props: ['param', 'editMode', 'errors'],
    data () {
        return {
            service_types: [{name: 'OPD', value: 'OPD'}, {name: 'ALL OPD', value: 'OPDALL'}, {name: 'IPD', value: 'IPD'}, {name: 'ALL IPD', value: 'IPDALL'}],
            item_types: ['SERVICE', 'PACKAGE'],
            form: {
                id: undefined,
                service_type: 'OPDALL',
                item_type: 'SERVICE',
                person_id: '',
                percentage_value: 0,
                status: 'ACTIVE',
            }
        }
    },
    created () {
        if (this.editMode) {
            this.form = this.param.data;
        }
    },
    methods: {
        reset: function () {
            this.form = {}
        },
        save: function (data) {
            console.log(data)
            if (this.form.service_type == 'OPD'){
                if (this.form.item_type == 'SERVICE'){
                    data['services'] = this.param.opd_services;
                }else{
                    data['services'] = this.param.opd_packages;
                }
            }
            if (this.form.service_type == 'IPD'){
                if (this.form.item_type == 'SERVICE'){
                    data['services'] = this.param.ipd_services;
                }else{
                    data['services'] = this.param.ipd_packages;
                }
            }
            this.$inertia.post('/referralSetting', data)
        },
        update: function (data) {
            data._method = 'PUT'
            this.$inertia.post('/referralSetting/' + data.id, data)
        }
    }
}
</script>
