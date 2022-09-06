<template>
    <div class="hospital-create">
        <slot name="header"></slot>
        <div class="row card-box">
            <div class="col-lg-8 offset-lg-2">
                <form>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Department Name</label>
                                <input class="form-control" type="text" v-model="form.name"/>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Department Hospital</label>
                                <select class="form-control select"  v-model="form.hospital_id">
                                    <option value="" selected>Select Hospital</option>
                                    <option  v-for="hospital in data.hospitals" :key="hospital.id" :value="hospital.id">{{ hospital.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Department Head</label>
                                <select class="form-control select"  v-model="form.dept_head_id">
                                    <option value="" selected>Select Department Head</option>
                                    <option  v-for="user in data.users" :key="user.id"  :value="user.id">{{ user.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Department Admin</label>
                                <select class="form-control select"  v-model="form.dept_admin_id">
                                    <option value="" selected>Select Department Admin</option>
                                    <option  v-for="user in data.users" :key="user.id"  :value="user.id">{{ user.name }}</option>
                                </select>
                            </div>
                        </div>


                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Department Status</label><br/>
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

export default {
    components: {
        Alert
    },
        props: ['data', 'errors', 'editMode'],
        data() {
            return {
                form: {
                    id: undefined,
                    name: null,
                    hospital_id: '',
                    dept_head_id: '',
                    dept_admin_id: '',
                    is_for_doctor: 0,
                    status: 'ACTIVE',
                },

            }
        },
        created() {
            if (this.editMode) {
                this.form = this.data
            }
        },
        methods: {
            reset: function () {
                this.form = {}
            },
            save: function (data) {
                this.$inertia.post('/departments', data)
            },
            edit: function (data) {
                this.form = Object.assign({}, data);
                this.editMode = true;
                this.openModal();
            },
            update: function (data) {
                data._method = 'PUT';
                this.$inertia.post('/departments/' + data.id, data)
            }
        }
    }
</script>
