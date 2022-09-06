<template>
    <app-layout>
        <Model :param="param" :errors="errors">
            <template #header>
                <Header
                    :title="title"
                    :flash="flash"
                    :errors="errors"
                    :link="link"
                    :label="label"
                />
            </template>
        </Model>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Model from './components/Model'
import Header from '@/Pages/Component/Header'
export default {
    components: {
        AppLayout,
        Model,
        Header
    },
    props: ['param', 'errors', 'flash'],
    data () {
        return {
            title: 'New Admission',
            link: 'patientAdmissions.index',
            label: 'Admitted Patient List'
        }
    }
}
</script>
