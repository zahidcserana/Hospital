<template>
    <div class="hospital-create">
        <slot name="header"></slot>
        <div class="row card-box">
            <div class="col-lg-8 offset-lg-2">
                <form>
                    <div class="row">

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Designation Title</label>
                                <input class="form-control" type="text" v-model="form.title"/>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Hospital</label>
                                <select class="form-control select"  v-model="form.hospital_id"  @change="getHospitalDepartments()" >
                                    <option value="" selected>Select Hospital</option>
                                    <option  v-for="hospital in data.hospitals" :key="hospital.id" :value="hospital.id">{{ hospital.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Department</label>
                                <select class="form-control select"  v-model="form.department_id">
                                    <option value="" selected>Select Department</option>
                                    <option  v-for="department in departments" :key="department.id"  :value="department.id">{{ department.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label> Description</label>
                                <textarea
                                    v-model="form.description"
                                    cols="30"
                                    rows="4"
                                    class="form-control"
                                ></textarea>
                            </div>
                        </div>


                    <div class="col-sm-6">
                        <div class="form-group">
                            <label> Status</label><br/>
                            <div class="form-check form-check-inline">
                                <input
                                    v-model="form.status"
                                    class="form-check-input"
                                    type="radio"
                                    name="status"
                                    value="ACTIVE"
                                />
                                <label
                                    class="form-check-label"
                                    for="product_active"
                                >
                                    Active
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input
                                    v-model="form.status"
                                    class="form-check-input"
                                    type="radio"
                                    name="status"
                                    value="INACTIVE"
                                />
                                <label
                                    class="form-check-label"
                                    for="product_inactive"
                                >
                                    Inactive
                                </label>
                            </div>
                        </div>
                    </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Is For Doctor</label><br/>
                                <div class="form-check form-check-inline">
                                    <input
                                        v-model="form.is_for_doctor"
                                        class="form-check-input"
                                        type="radio"
                                        name="status"
                                        value="1"
                                    />
                                    <label
                                        class="form-check-label"
                                    >
                                        YES
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input
                                        v-model="form.is_for_doctor"
                                        class="form-check-input"
                                        type="radio"
                                        name="status"
                                        value="0"
                                    />
                                    <label
                                        class="form-check-label"
                                    >
                                        No
                                    </label>
                                </div>
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
</template>

<script>
import Alert from '@/Pages/Component/Alert'
import {HTTP} from '@/utils/http-common'

export default {
    components: {
        Alert
    },
        props: ['data', 'errors', 'editMode'],
        data() {
            return {
                form: {
                    id: undefined,
                    title: null,
                    description: null,
                    hospital_id: '',
                    department_id: '',
                    is_for_doctor: 0,
                    status: 'ACTIVE',
                },
                departments: []
            }
        },
        created() {
            if (this.editMode) {
                this.form = this.data;
                this.getHospitalDepartments()
            }
        },
        methods: {
            reset: function () {
                this.form = {}
            },
            save: function (data) {
                this.$inertia.post('/designations', data)
            },
            edit: function (data) {
                this.form = Object.assign({}, data);
                this.editMode = true;
                this.openModal();
            },
            update: function (data) {
                data._method = 'PUT';
                this.$inertia.post('/designations/' + data.id, data)
            },
            getHospitalDepartments: function (){
                HTTP.get('/hospitals/'+this.form.hospital_id+'/departments')
                    .then((response)=>{
                        this.departments = response.data.departments
                    })
            }
        }
    }
</script>
